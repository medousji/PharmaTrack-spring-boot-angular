package com.pharmatrack.auth.service;

import com.pharmatrack.auth.dto.AuthUserResponse;
import com.pharmatrack.auth.dto.LoginRequest;
import com.pharmatrack.auth.dto.RefreshRequest;
import com.pharmatrack.auth.dto.RegisterRequest;
import com.pharmatrack.auth.dto.TokenResponse;
import com.pharmatrack.auth.entity.User;
import com.pharmatrack.auth.entity.UserRole;
import com.pharmatrack.auth.entity.UserStatus;
import com.pharmatrack.auth.mapper.AuthUserMapper;
import com.pharmatrack.auth.repository.UserRepository;
import com.pharmatrack.catalog.service.NotificationService;
import com.pharmatrack.common.error.ConflictException;
import com.pharmatrack.common.error.ForbiddenException;
import com.pharmatrack.common.error.UnauthorizedException;
import com.pharmatrack.common.security.JwtProperties;
import com.pharmatrack.common.security.JwtTokenProvider;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.time.Instant;
import java.util.List;
import java.util.UUID;

/**
 * Auth flows. Login issues a real JWT access + refresh pair (the single most
 * important functional fix over the legacy app, which never issued API tokens).
 */
@Service
public class AuthService {

    private final UserRepository userRepository;
    private final PasswordEncoder passwordEncoder;
    private final JwtTokenProvider tokenProvider;
    private final RefreshTokenService refreshTokenService;
    private final AuthUserMapper mapper;
    private final JwtProperties jwtProperties;
    private final NotificationService notificationService;

    public AuthService(UserRepository userRepository,
                       PasswordEncoder passwordEncoder,
                       JwtTokenProvider tokenProvider,
                       RefreshTokenService refreshTokenService,
                       AuthUserMapper mapper,
                       JwtProperties jwtProperties,
                       NotificationService notificationService) {
        this.userRepository = userRepository;
        this.passwordEncoder = passwordEncoder;
        this.tokenProvider = tokenProvider;
        this.refreshTokenService = refreshTokenService;
        this.mapper = mapper;
        this.jwtProperties = jwtProperties;
        this.notificationService = notificationService;
    }

    /**
     * Register a new account. Pending admin approval (isApproved=false,
     * role=visiteur) until an admin approves it - the admin approval workflow.
     */
    @Transactional
    public AuthUserResponse register(RegisterRequest request) {
        if (userRepository.existsByEmail(request.email().toLowerCase())) {
            throw new ConflictException("An account with email '" + request.email() + "' already exists.");
        }
        User user = new User();
        user.setName(request.name());
        user.setEmail(request.email().toLowerCase());
        user.setPasswordHash(passwordEncoder.encode(request.password()));
        user.setRole(UserRole.visiteur);
        user.setStatus(UserStatus.active);
        user.setApproved(false);
        User saved = userRepository.save(user);
        for (User admin : userRepository.findByRoleIn(List.of(UserRole.admin))) {
            notificationService.nouveauCompteEnAttente(saved);
        }
        return mapper.toResponse(saved);
    }

    /**
     * Authenticate and issue a real JWT access + refresh token pair.
     */
    @Transactional
    public TokenResponse login(LoginRequest request, String clientIp) {
        User user = userRepository.findByEmail(request.email().toLowerCase())
                .orElseThrow(() -> new UnauthorizedException("Invalid email or password."));

        if (!passwordEncoder.matches(request.password(), user.getPasswordHash())) {
            throw new UnauthorizedException("Invalid email or password.");
        }
        if (user.getStatus() != UserStatus.active) {
            throw new ForbiddenException("Account is " + user.getStatus() + ".");
        }
        if (!user.isApproved()) {
            throw new ForbiddenException("Account is awaiting admin approval.");
        }

        user.setLastLoginAt(Instant.now());
        user.setLastLoginIp(clientIp);
        userRepository.save(user);

        return issueTokens(user);
    }

    /**
     * Refresh: validate + rotate the refresh token, issue a new access token
     * and a rotated refresh token.
     */
    @Transactional
    public TokenResponse refresh(RefreshRequest request) {
        var claims = tokenProvider.parseRefreshToken(request.refreshToken())
                .orElseThrow(() -> new UnauthorizedException("Invalid refresh token."));
        User user = userRepository.findById(claims.userId())
                .orElseThrow(() -> new UnauthorizedException("User no longer exists."));
        if (user.getStatus() != UserStatus.active || !user.isApproved()) {
            throw new ForbiddenException("Account is not active/approved.");
        }
        refreshTokenService.rotate(request.refreshToken(), user);
        return issueTokens(user);
    }

    @Transactional
    public void logout(String refreshToken) {
        refreshTokenService.revoke(refreshToken);
    }

    @Transactional(readOnly = true)
    public AuthUserResponse me(UUID userId) {
        User user = userRepository.findById(userId)
                .orElseThrow(() -> new UnauthorizedException("User not found."));
        return mapper.toResponse(user);
    }

    private TokenResponse issueTokens(User user) {
        String accessToken = tokenProvider.issueAccessToken(user);
        RefreshTokenService.IssuedRefreshToken refresh = refreshTokenService.issue(user);
        long expiresIn = jwtProperties.getAccessTokenTtl().toSeconds();
        return new TokenResponse(accessToken, refresh.raw(), expiresIn, "Bearer",
                mapper.toResponse(user));
    }
}
