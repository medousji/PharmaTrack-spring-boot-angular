package com.pharmatrack.auth.it;

import com.pharmatrack.auth.entity.User;
import com.pharmatrack.auth.entity.UserRole;
import com.pharmatrack.auth.repository.UserRepository;
import com.fasterxml.jackson.databind.JsonNode;
import com.fasterxml.jackson.databind.ObjectMapper;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.Test;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.test.autoconfigure.web.servlet.AutoConfigureMockMvc;
import org.springframework.boot.test.context.SpringBootTest;
import org.springframework.http.MediaType;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.test.context.DynamicPropertyRegistry;
import org.springframework.test.context.DynamicPropertySource;
import org.springframework.test.web.servlet.MockMvc;

import java.util.UUID;

import static org.assertj.core.api.Assertions.assertThat;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.get;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.post;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.jsonPath;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.status;

/**
 * End-to-end auth flow against the local PostgreSQL instance (localhost:5433),
 * running without Docker. Requires the database "pharmatrack_test" to exist on
 * the local cluster (created with the pharmatrack role as owner).
 */
@SpringBootTest
@AutoConfigureMockMvc
class AuthFlowIntegrationTest {

    @DynamicPropertySource
    static void datasource(DynamicPropertyRegistry registry) {
        registry.add("spring.datasource.url",
                () -> "jdbc:postgresql://localhost:5433/pharmatrack_test");
        registry.add("spring.datasource.username", () -> "pharmatrack");
        registry.add("spring.datasource.password", () -> "pharmatrack");
    }

    @Autowired MockMvc mockMvc;
    @Autowired ObjectMapper objectMapper;
    @Autowired JdbcTemplate jdbcTemplate;
    @Autowired UserRepository userRepository;

    @BeforeEach
    void clean() {
        jdbcTemplate.execute("DELETE FROM refresh_tokens");
        jdbcTemplate.execute("DELETE FROM users");
    }

    @Test
    void fullAuthLifecycle() throws Exception {
        // 1. Register a new user -> created, unapproved visiteur
        String registerBody = """
                {"email":"integration@test.com","password":"S3cret-pass!","name":"Test User"}""";
        String registerJson = mockMvc.perform(post("/api/v1/auth/register")
                        .contentType(MediaType.APPLICATION_JSON).content(registerBody))
                .andExpect(status().isCreated())
                .andExpect(jsonPath("$.role").value("visiteur"))
                .andExpect(jsonPath("$.status").value("active"))
                .andReturn().getResponse().getContentAsString();
        UUID userId = UUID.fromString(
                objectMapper.readTree(registerJson).path("id").asText());

        // 2. Login while unapproved -> 403
        String loginBody = """
                {"email":"integration@test.com","password":"S3cret-pass!"}""";
        mockMvc.perform(post("/api/v1/auth/login")
                        .contentType(MediaType.APPLICATION_JSON).content(loginBody))
                .andExpect(status().isForbidden());

        // 3. Admin approves the user (simulated via repository) -> pharmacien
        User user = userRepository.findById(userId).orElseThrow();
        user.setApproved(true);
        user.setRole(UserRole.pharmacien);
        userRepository.save(user);

        // 4. Login now succeeds and returns access + refresh tokens
        String loginJson = mockMvc.perform(post("/api/v1/auth/login")
                        .contentType(MediaType.APPLICATION_JSON).content(loginBody))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$.accessToken").isNotEmpty())
                .andExpect(jsonPath("$.refreshToken").isNotEmpty())
                .andExpect(jsonPath("$.tokenType").value("Bearer"))
                .andReturn().getResponse().getContentAsString();
        JsonNode login = objectMapper.readTree(loginJson);
        String accessToken = login.path("accessToken").asText();
        String refreshToken = login.path("refreshToken").asText();

        // 5. Use the access token against a protected endpoint
        mockMvc.perform(get("/api/v1/auth/me")
                        .header("Authorization", "Bearer " + accessToken))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$.email").value("integration@test.com"))
                .andExpect(jsonPath("$.role").value("pharmacien"));

        // 6. Protected admin endpoint is NOT accessible without a token
        mockMvc.perform(get("/api/v1/admin/users/pending"))
                .andExpect(status().isUnauthorized());

        // 7. Refresh rotation -> new access + refresh, old refresh becomes unusable
        String refreshBody = "{\"refreshToken\":\"" + refreshToken + "\"}";
        String refreshJson = mockMvc.perform(post("/api/v1/auth/refresh")
                        .contentType(MediaType.APPLICATION_JSON).content(refreshBody))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$.accessToken").isNotEmpty())
                .andReturn().getResponse().getContentAsString();
        String rotatedAccess = objectMapper.readTree(refreshJson).path("accessToken").asText();
        String newRefresh = objectMapper.readTree(refreshJson).path("refreshToken").asText();

        // old refresh token now rejected
        mockMvc.perform(post("/api/v1/auth/refresh")
                        .contentType(MediaType.APPLICATION_JSON).content(refreshBody))
                .andExpect(status().isUnauthorized());

        // rotated access token still works
        mockMvc.perform(get("/api/v1/auth/me")
                        .header("Authorization", "Bearer " + rotatedAccess))
                .andExpect(status().isOk());

        // 8. Logout with current access token revokes the refresh family
        mockMvc.perform(post("/api/v1/auth/logout")
                        .header("Authorization", "Bearer " + rotatedAccess)
                        .contentType(MediaType.APPLICATION_JSON)
                        .content("{\"refreshToken\":\"" + newRefresh + "\"}"))
                .andExpect(status().isNoContent());

        // after logout the refresh token is rejected
        mockMvc.perform(post("/api/v1/auth/refresh")
                        .contentType(MediaType.APPLICATION_JSON)
                        .content("{\"refreshToken\":\"" + newRefresh + "\"}"))
                .andExpect(status().isUnauthorized());

        assertThat(userRepository.count()).isEqualTo(1);
    }
}
