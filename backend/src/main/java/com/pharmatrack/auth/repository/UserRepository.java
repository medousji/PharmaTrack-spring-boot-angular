package com.pharmatrack.auth.repository;

import com.pharmatrack.auth.entity.User;
import com.pharmatrack.auth.entity.UserRole;
import com.pharmatrack.auth.entity.UserStatus;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;

import java.util.Collection;
import java.util.List;
import java.util.Optional;
import java.util.UUID;

public interface UserRepository extends JpaRepository<User, UUID> {

    Optional<User> findByEmail(String email);

    List<User> findByRoleIn(Collection<UserRole> roles);

    boolean existsByEmail(String email);

    Page<User> findByIsApproved(boolean isApproved, Pageable pageable);

    List<User> findByIsApprovedFalse();

    Page<User> findByStatus(UserStatus status, Pageable pageable);

    long countByRole(UserRole role);

    long countByStatus(UserStatus status);

    long countByIsApprovedFalse();

    @Query("""
            SELECT u FROM User u
            WHERE (lower(u.name) LIKE lower(concat('%', :search, '%'))
                   OR lower(u.email) LIKE lower(concat('%', :search, '%')))
              AND (:role IS NULL OR u.role = :role)
              AND (:statut IS NULL OR u.status = :statut)
            """)
    Page<User> search(@Param("search") String search,
                      @Param("role") UserRole role,
                      @Param("statut") UserStatus statut,
                      Pageable pageable);
}
