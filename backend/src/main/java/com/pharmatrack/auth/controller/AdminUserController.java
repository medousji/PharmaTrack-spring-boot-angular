package com.pharmatrack.auth.controller;

import com.pharmatrack.auth.dto.AdminUserStatsResponse;
import com.pharmatrack.auth.dto.ApproveRequest;
import com.pharmatrack.auth.dto.AuthUserResponse;
import com.pharmatrack.auth.dto.CreateUserRequest;
import com.pharmatrack.auth.dto.UpdateUserRequest;
import com.pharmatrack.auth.entity.UserRole;
import com.pharmatrack.auth.entity.UserStatus;
import com.pharmatrack.auth.service.AdminUserService;
import com.pharmatrack.common.api.PagedResponse;
import com.pharmatrack.common.security.CurrentUser;
import io.swagger.v3.oas.annotations.tags.Tag;
import jakarta.validation.Valid;
import org.springframework.data.domain.Page;
import org.springframework.http.HttpStatus;
import org.springframework.http.MediaType;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.web.bind.annotation.DeleteMapping;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.PutMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.ResponseStatus;
import org.springframework.web.bind.annotation.RestController;

import java.util.UUID;

@Tag(name = "Admin Users")
@RestController
@RequestMapping(value = "/api/v1/admin/users", produces = MediaType.APPLICATION_JSON_VALUE)
@PreAuthorize("hasRole('ADMIN')")
public class AdminUserController {

    private final AdminUserService service;
    private final CurrentUser currentUser;

    public AdminUserController(AdminUserService service, CurrentUser currentUser) {
        this.service = service;
        this.currentUser = currentUser;
    }

    @GetMapping
    public PagedResponse<AuthUserResponse> list(
            @RequestParam(required = false) String search,
            @RequestParam(required = false) UserRole role,
            @RequestParam(required = false) UserStatus statut,
            @RequestParam(defaultValue = "0") Integer page,
            @RequestParam(defaultValue = "20") Integer size) {
        Page<AuthUserResponse> result = service.listAll(search, role, statut, page, size);
        return PagedResponse.from(result);
    }

    @GetMapping("/stats")
    public AdminUserStatsResponse stats() {
        return service.stats();
    }

    @GetMapping("/{id}")
    public AuthUserResponse getById(@PathVariable UUID id) {
        return service.getById(id);
    }

    @GetMapping("/pending")
    public PagedResponse<AuthUserResponse> pending(
            @RequestParam(defaultValue = "0") Integer page,
            @RequestParam(defaultValue = "20") Integer size) {
        Page<AuthUserResponse> result = service.listPending(page, size);
        return PagedResponse.from(result);
    }

    @PostMapping
    @ResponseStatus(HttpStatus.CREATED)
    public AuthUserResponse create(@Valid @RequestBody CreateUserRequest request) {
        return service.create(request);
    }

    @PutMapping("/{id}")
    public AuthUserResponse update(@PathVariable UUID id,
                                   @Valid @RequestBody UpdateUserRequest request) {
        return service.update(id, request);
    }

    @DeleteMapping("/{id}")
    @ResponseStatus(HttpStatus.NO_CONTENT)
    public void delete(@PathVariable UUID id) {
        service.delete(id, currentUser.id().orElse(null));
    }

    @PostMapping("/{id}/reject")
    @ResponseStatus(HttpStatus.NO_CONTENT)
    public void reject(@PathVariable UUID id) {
        service.reject(id);
    }

    @PostMapping("/{id}/approve")
    public AuthUserResponse approve(@PathVariable UUID id,
                                    @Valid @RequestBody ApproveRequest request) {
        return service.approve(id, request);
    }
}