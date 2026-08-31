package com.pharmatrack.auth.service;

import com.pharmatrack.auth.dto.AdminUserStatsResponse;
import com.pharmatrack.auth.dto.ApproveRequest;
import com.pharmatrack.auth.dto.AuthUserResponse;
import com.pharmatrack.auth.dto.CreateUserRequest;
import com.pharmatrack.auth.dto.UpdateUserRequest;
import com.pharmatrack.auth.entity.User;
import com.pharmatrack.auth.entity.UserRole;
import com.pharmatrack.auth.entity.UserStatus;
import com.pharmatrack.auth.mapper.AuthUserMapper;
import com.pharmatrack.auth.repository.PharmacieRepository;
import com.pharmatrack.auth.repository.RefreshTokenRepository;
import com.pharmatrack.auth.repository.UserRepository;
import com.pharmatrack.catalog.service.NotificationService;
import com.pharmatrack.common.error.ConflictException;
import com.pharmatrack.common.error.ResourceNotFoundException;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.PageRequest;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.time.Instant;
import java.util.LinkedHashMap;
import java.util.Locale;
import java.util.Map;
import java.util.UUID;

/**
 * Admin user management: list/search accounts, create/edit/delete them, the
 * pending approval workflow, and aggregate counters. Guards mirror the legacy
 * behaviour: the last admin cannot be demoted, deactivated or deleted, and
 * nobody can delete their own account.
 */
@Service
public class AdminUserService {

    private final UserRepository userRepository;
    private final PharmacieRepository pharmacieRepository;
    private final RefreshTokenRepository refreshTokenRepository;
    private final PasswordEncoder passwordEncoder;
    private final AuthUserMapper mapper;
    private final NotificationService notificationService;

    public AdminUserService(UserRepository userRepository,
                            PharmacieRepository pharmacieRepository,
                            RefreshTokenRepository refreshTokenRepository,
                            PasswordEncoder passwordEncoder,
                            AuthUserMapper mapper,
                            NotificationService notificationService) {
        this.userRepository = userRepository;
        this.pharmacieRepository = pharmacieRepository;
        this.refreshTokenRepository = refreshTokenRepository;
        this.passwordEncoder = passwordEncoder;
        this.mapper = mapper;
        this.notificationService = notificationService;
    }

    @Transactional(readOnly = true)
    public Page<AuthUserResponse> listPending(int page, int size) {
        Page<User> users = userRepository.findByIsApproved(false,
                PageRequest.of(Math.max(page, 0), Math.min(Math.max(size, 1), 100)));
        return users.map(mapper::toResponse);
    }

    @Transactional(readOnly = true)
    public Page<AuthUserResponse> listAll(String search, UserRole role, UserStatus statut,
                                          int page, int size) {
        Page<User> users = userRepository.search(
                search == null ? "" : search.trim(), role, statut,
                PageRequest.of(Math.max(page, 0), Math.min(Math.max(size, 1), 100)));
        return users.map(mapper::toResponse);
    }

    @Transactional(readOnly = true)
    public AuthUserResponse getById(UUID userId) {
        return mapper.toResponse(get(userId));
    }

    @Transactional(readOnly = true)
    public AdminUserStatsResponse stats() {
        Map<String, Long> parStatut = new LinkedHashMap<>();
        for (UserStatus s : UserStatus.values()) {
            parStatut.put(s.name(), userRepository.countByStatus(s));
        }
        return new AdminUserStatsResponse(
                userRepository.count(),
                userRepository.countByIsApprovedFalse(),
                userRepository.countByRole(UserRole.admin),
                userRepository.countByRole(UserRole.pharmacien),
                userRepository.countByRole(UserRole.fournisseur),
                userRepository.countByRole(UserRole.visiteur),
                parStatut);
    }

    @Transactional
    public AuthUserResponse create(CreateUserRequest request) {
        if (userRepository.existsByEmail(request.email())) {
            throw new ConflictException("Un utilisateur avec l'adresse « " + request.email()
                    + " » existe déjà.");
        }
        User user = new User();
        user.setName(request.name());
        user.setEmail(request.email().trim().toLowerCase(Locale.ROOT));
        user.setPasswordHash(passwordEncoder.encode(request.password()));
        user.setRole(request.role());
        user.setStatus(request.status() != null ? request.status() : UserStatus.active);
        user.setApproved(true);
        user.setApprovedAt(Instant.now());
        if (request.pharmacieId() != null) {
            user.setPharmacie(resolvePharmacie(request.pharmacieId()));
        }
        return mapper.toResponse(userRepository.save(user));
    }

    @Transactional
    public AuthUserResponse update(UUID userId, UpdateUserRequest request) {
        User user = get(userId);
        if (!user.getEmail().equalsIgnoreCase(request.email())
                && userRepository.existsByEmail(request.email())) {
            throw new ConflictException("Un utilisateur avec l'adresse « " + request.email()
                    + " » existe déjà.");
        }
        guardLastAdmin(user, request.role(), request.status());

        user.setName(request.name());
        user.setEmail(request.email().trim().toLowerCase(Locale.ROOT));
        if (request.role() != null) {
            user.setRole(request.role());
        }
        if (request.status() != null) {
            user.setStatus(request.status());
        }
        if (request.pharmacieId() != null) {
            user.setPharmacie(resolvePharmacie(request.pharmacieId()));
        }
        if (request.password() != null && !request.password().isBlank()) {
            user.setPasswordHash(passwordEncoder.encode(request.password()));
        }
        return mapper.toResponse(userRepository.save(user));
    }

    @Transactional
    public void delete(UUID userId, UUID requesterId) {
        User user = get(userId);
        if (user.getId().equals(requesterId)) {
            throw new ConflictException("Vous ne pouvez pas supprimer votre propre compte.");
        }
        if (user.getRole() == UserRole.admin && userRepository.countByRole(UserRole.admin) <= 1) {
            throw new ConflictException("Impossible de supprimer le dernier administrateur.");
        }
        refreshTokenRepository.deleteByUserId(user.getId());
        userRepository.delete(user);
    }

    /**
     * Reject a pending registration: the account is deactivated so it can
     * never log in (mirrors the legacy "reject" of a pending request).
     */
    @Transactional
    public void reject(UUID userId) {
        User user = get(userId);
        user.setApproved(false);
        user.setStatus(UserStatus.inactive);
        userRepository.save(user);
    }

    /**
     * Approve a user. When {@code approved=false}, the request is a rejection:
     * the account is set to inactive so it can never log in.
     */
    @Transactional
    public AuthUserResponse approve(UUID userId, ApproveRequest request) {
        User user = get(userId);
        if (request.approved()) {
            user.setApproved(true);
            user.setApprovedAt(Instant.now());
            user.setStatus(UserStatus.active);
            if (request.role() != null) {
                user.setRole(request.role());
            }
            if (request.pharmacieId() != null) {
                user.setPharmacie(resolvePharmacie(request.pharmacieId()));
            }
            notificationService.compteApprouve(user);
        } else {
            user.setApproved(false);
            user.setStatus(UserStatus.inactive);
        }
        return mapper.toResponse(userRepository.save(user));
    }

    private User get(UUID userId) {
        return userRepository.findById(userId)
                .orElseThrow(() -> new ResourceNotFoundException("user", userId));
    }

    private com.pharmatrack.auth.entity.Pharmacie resolvePharmacie(UUID pharmacieId) {
        return pharmacieRepository.findById(pharmacieId)
                .orElseThrow(() -> new ResourceNotFoundException("pharmacie", pharmacieId));
    }

    private void guardLastAdmin(User target, UserRole newRole, UserStatus newStatus) {
        UserRole role = newRole != null ? newRole : target.getRole();
        UserStatus statut = newStatus != null ? newStatus : target.getStatus();
        boolean demotion = target.getRole() == UserRole.admin
                && (role != UserRole.admin || statut != UserStatus.active);
        if (demotion && userRepository.countByRole(UserRole.admin) <= 1) {
            throw new ConflictException("Impossible de modifier le dernier administrateur.");
        }
    }
}