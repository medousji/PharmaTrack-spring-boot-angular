package com.pharmatrack.auth.service;

import com.pharmatrack.auth.dto.LoginRequest;
import com.pharmatrack.auth.dto.RegisterRequest;
import com.pharmatrack.auth.dto.TokenResponse;
import com.pharmatrack.auth.entity.User;
import com.pharmatrack.auth.entity.UserRole;
import com.pharmatrack.auth.entity.UserStatus;
import com.pharmatrack.auth.mapper.AuthUserMapper;
import com.pharmatrack.auth.repository.UserRepository;
import com.pharmatrack.common.error.ConflictException;
import com.pharmatrack.common.error.ForbiddenException;
import com.pharmatrack.common.error.UnauthorizedException;
import com.pharmatrack.common.security.JwtProperties;
import com.pharmatrack.common.security.JwtTokenProvider;
import com.pharmatrack.common.security.AuthPrincipal;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.extension.ExtendWith;
import org.mockito.Mock;
import org.mockito.junit.jupiter.MockitoExtension;
import org.springframework.security.crypto.password.PasswordEncoder;

import java.time.Duration;
import java.util.Optional;
import java.util.UUID;

import static org.assertj.core.api.Assertions.assertThat;
import static org.assertj.core.api.Assertions.assertThatThrownBy;
import static org.mockito.ArgumentMatchers.any;
import static org.mockito.Mockito.verify;
import static org.mockito.Mockito.when;

@ExtendWith(MockitoExtension.class)
class AuthServiceTest {

    @Mock UserRepository userRepository;
    @Mock PasswordEncoder passwordEncoder;
    @Mock JwtTokenProvider tokenProvider;
    @Mock RefreshTokenService refreshTokenService;
    @Mock AuthUserMapper mapper;
    @Mock com.pharmatrack.catalog.service.NotificationService notificationService;
    JwtProperties jwtProperties;

    AuthService service;

    @BeforeEach
    void setUp() {
        jwtProperties = new JwtProperties();
        jwtProperties.setAccessTokenTtl(Duration.ofMinutes(15));
        jwtProperties.setRefreshTokenTtl(Duration.ofDays(7));
        service = new AuthService(userRepository, passwordEncoder, tokenProvider,
                refreshTokenService, mapper, jwtProperties, notificationService);
    }

    private User approvedUser() {
        User u = new User();
        u.setId(UUID.randomUUID());
        u.setEmail("pharma@test.com");
        u.setPasswordHash("hashed");
        u.setRole(UserRole.pharmacien);
        u.setStatus(UserStatus.active);
        u.setApproved(true);
        return u;
    }

    @Test
    void registerCreatesUnapprovedVisiteur() {
        RegisterRequest req = new RegisterRequest("Nadia", "nadia@test.com", "password123", null);
        when(userRepository.existsByEmail("nadia@test.com")).thenReturn(false);
        when(passwordEncoder.encode("password123")).thenReturn("encoded");

        service.register(req);

        org.mockito.ArgumentCaptor<User> captor = org.mockito.ArgumentCaptor.forClass(User.class);
        verify(userRepository).save(captor.capture());
        User saved = captor.getValue();
        assertThat(saved.getRole()).isEqualTo(UserRole.visiteur);
        assertThat(saved.isApproved()).isFalse();
        assertThat(saved.getStatus()).isEqualTo(UserStatus.active);
        assertThat(saved.getPasswordHash()).isEqualTo("encoded");
    }

    @Test
    void registerRejectsDuplicateEmail() {
        RegisterRequest req = new RegisterRequest("Nadia", "nadia@test.com", "password123", null);
        when(userRepository.existsByEmail("nadia@test.com")).thenReturn(true);
        assertThatThrownBy(() -> service.register(req)).isInstanceOf(ConflictException.class);
    }

    @Test
    void loginIssuesTokensForApprovedActiveUser() {
        User user = approvedUser();
        when(userRepository.findByEmail("pharma@test.com")).thenReturn(Optional.of(user));
        when(passwordEncoder.matches("goodpass", "hashed")).thenReturn(true);
        when(tokenProvider.issueAccessToken(user)).thenReturn("ACCESS");
        when(refreshTokenService.issue(user))
                .thenReturn(new RefreshTokenService.IssuedRefreshToken("REFRESH", UUID.randomUUID(), null));

        TokenResponse response = service.login(new LoginRequest("pharma@test.com", "goodpass"), "127.0.0.1");

        assertThat(response.accessToken()).isEqualTo("ACCESS");
        assertThat(response.refreshToken()).isEqualTo("REFRESH");
        assertThat(response.tokenType()).isEqualTo("Bearer");
        verify(userRepository).save(user);
    }

    @Test
    void loginRejectsWrongPassword() {
        User user = approvedUser();
        when(userRepository.findByEmail("pharma@test.com")).thenReturn(Optional.of(user));
        when(passwordEncoder.matches("wrongpass", "hashed")).thenReturn(false);
        assertThatThrownBy(() -> service.login(
                new LoginRequest("pharma@test.com", "wrongpass"), "127.0.0.1"))
                .isInstanceOf(UnauthorizedException.class);
    }

    @Test
    void loginRejectsUnapprovedUser() {
        User user = approvedUser();
        user.setApproved(false);
        when(userRepository.findByEmail("pharma@test.com")).thenReturn(Optional.of(user));
        when(passwordEncoder.matches("goodpass", "hashed")).thenReturn(true);
        assertThatThrownBy(() -> service.login(
                new LoginRequest("pharma@test.com", "goodpass"), "127.0.0.1"))
                .isInstanceOf(ForbiddenException.class);
    }

    @Test
    void loginRejectsUnknownEmail() {
        when(userRepository.findByEmail("nobody@test.com")).thenReturn(Optional.empty());
        assertThatThrownBy(() -> service.login(
                new LoginRequest("nobody@test.com", "whatever"), "127.0.0.1"))
                .isInstanceOf(UnauthorizedException.class);
    }

    @Test
    void loginRejectsSuspendedUser() {
        User user = approvedUser();
        user.setStatus(UserStatus.suspended);
        when(userRepository.findByEmail("pharma@test.com")).thenReturn(Optional.of(user));
        when(passwordEncoder.matches("goodpass", "hashed")).thenReturn(true);
        assertThatThrownBy(() -> service.login(
                new LoginRequest("pharma@test.com", "goodpass"), "127.0.0.1"))
                .isInstanceOf(ForbiddenException.class);
    }
}
