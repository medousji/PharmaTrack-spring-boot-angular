package com.pharmatrack.common.security;

import com.pharmatrack.auth.entity.User;
import com.pharmatrack.auth.entity.UserRole;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.Test;

import java.time.Duration;
import java.util.Optional;
import java.util.UUID;

import static org.assertj.core.api.Assertions.assertThat;

class JwtTokenProviderTest {

    private JwtTokenProvider provider;

    private static final String SECRET =
            "U2VjdXJlLURldi9Qcm9kLUtleS1DaGFuZ2UtTWUtNjQrQnl0ZXMtU3VwZXItTG9uZy1Gb3ItSFMyNTZBLUhlYWx0aC1GdW5jdGlvbg==";

    @BeforeEach
    void setUp() {
        JwtProperties props = new JwtProperties();
        props.setSecret(SECRET);
        props.setAccessTokenTtl(Duration.ofMinutes(15));
        props.setRefreshTokenTtl(Duration.ofDays(7));
        provider = new JwtTokenProvider(props);
    }

    @Test
    void accessTokenRoundTrip() {
        User user = new User();
        user.setId(UUID.randomUUID());
        user.setRole(UserRole.pharmacien);

        String token = provider.issueAccessToken(user);
        Optional<AuthPrincipal> parsed = provider.parseAccessToken(token);

        assertThat(parsed).isPresent();
        assertThat(parsed.get().id()).isEqualTo(user.getId());
        assertThat(parsed.get().role()).isEqualTo("pharmacien");
    }

    @Test
    void refreshTokenCannotBeParsedAsAccessToken() {
        User user = new User();
        user.setId(UUID.randomUUID());

        String refresh = provider.issueRefreshToken(user, UUID.randomUUID());
        assertThat(provider.parseAccessToken(refresh)).isEmpty();
        assertThat(provider.parseRefreshToken(refresh)).isPresent();
    }

    @Test
    void accessTokenCannotBeParsedAsRefreshToken() {
        User user = new User();
        user.setId(UUID.randomUUID());

        String access = provider.issueAccessToken(user);
        assertThat(provider.parseRefreshToken(access)).isEmpty();
    }

    @Test
    void tamperedTokenIsRejected() {
        User user = new User();
        user.setId(UUID.randomUUID());
        String token = provider.issueAccessToken(user);

        String tampered = token.substring(0, token.length() - 4) + "XXXX";
        assertThat(provider.parseAccessToken(tampered)).isEmpty();
    }

    @Test
    void garbageTokenIsRejected() {
        assertThat(provider.parseAccessToken("not.a.jwt")).isEmpty();
    }
}
