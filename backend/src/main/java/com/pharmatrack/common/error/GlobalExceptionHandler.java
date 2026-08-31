package com.pharmatrack.common.error;

import jakarta.servlet.http.HttpServletRequest;
import jakarta.validation.ConstraintViolationException;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.http.converter.HttpMessageNotReadableException;
import org.springframework.security.access.AccessDeniedException;
import org.springframework.validation.FieldError;
import org.springframework.web.bind.MethodArgumentNotValidException;
import org.springframework.web.bind.annotation.ExceptionHandler;
import org.springframework.web.bind.annotation.RestControllerAdvice;
import org.springframework.web.method.annotation.MethodArgumentTypeMismatchException;

import java.util.ArrayList;
import java.util.List;

/**
 * Central mapping of exceptions to RFC 7807 problem+json responses.
 * Controllers never catch or translate errors themselves; they simply
 * throw domain exceptions and let this handler produce the payload.
 */
@RestControllerAdvice
public class GlobalExceptionHandler {

    private static final Logger log = LoggerFactory.getLogger(GlobalExceptionHandler.class);

    @ExceptionHandler(ApiException.class)
    public ResponseEntity<ProblemResponse> handleApi(ApiException ex, HttpServletRequest req) {
        ProblemResponse body = new ProblemResponse(
                "about:blank", ex.getTitle(), ex.getStatus(), ex.getMessage(), req.getRequestURI());
        return ResponseEntity.status(ex.getStatus()).body(body);
    }

    @ExceptionHandler(MethodArgumentNotValidException.class)
    public ResponseEntity<ProblemResponse> handleValidation(MethodArgumentNotValidException ex, HttpServletRequest req) {
        List<ProblemResponse.FieldError> errors = new ArrayList<>();
        for (FieldError fe : ex.getBindingResult().getFieldErrors()) {
            errors.add(new ProblemResponse.FieldError(fe.getField(), fe.getDefaultMessage()));
        }
        ProblemResponse body = new ProblemResponse(
                "about:blank", "Validation failed", 400, "One or more fields are invalid.", req.getRequestURI());
        body.setErrors(errors);
        return ResponseEntity.status(HttpStatus.BAD_REQUEST).body(body);
    }

    @ExceptionHandler(ConstraintViolationException.class)
    public ResponseEntity<ProblemResponse> handleConstraintViolation(ConstraintViolationException ex, HttpServletRequest req) {
        List<ProblemResponse.FieldError> errors = new ArrayList<>();
        ex.getConstraintViolations().forEach(cv ->
                errors.add(new ProblemResponse.FieldError(cv.getPropertyPath().toString(), cv.getMessage())));
        ProblemResponse body = new ProblemResponse(
                "about:blank", "Validation failed", 400, "One or more parameters are invalid.", req.getRequestURI());
        body.setErrors(errors);
        return ResponseEntity.status(HttpStatus.BAD_REQUEST).body(body);
    }

    @ExceptionHandler({HttpMessageNotReadableException.class, MethodArgumentTypeMismatchException.class})
    public ResponseEntity<ProblemResponse> handleUnreadable(Exception ex, HttpServletRequest req) {
        ProblemResponse body = new ProblemResponse(
                "about:blank", "Malformed request", 400, ex.getMessage(), req.getRequestURI());
        return ResponseEntity.status(HttpStatus.BAD_REQUEST).body(body);
    }

    @ExceptionHandler(AccessDeniedException.class)
    public ResponseEntity<ProblemResponse> handleAccessDenied(AccessDeniedException ex, HttpServletRequest req) {
        ProblemResponse body = new ProblemResponse(
                "about:blank", "Forbidden", 403, "You are not authorized to perform this action.", req.getRequestURI());
        return ResponseEntity.status(HttpStatus.FORBIDDEN).body(body);
    }

    @ExceptionHandler({org.springframework.http.converter.HttpMessageNotWritableException.class})
    public ResponseEntity<ProblemResponse> handleNotWritable(Exception ex, HttpServletRequest req) {
        ProblemResponse body = new ProblemResponse(
                "about:blank", "Internal error", 500, "Response serialization failed.", req.getRequestURI());
        return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(body);
    }

    @ExceptionHandler(Exception.class)
    public ResponseEntity<ProblemResponse> handleGeneric(Exception ex, HttpServletRequest req) {
        log.error("Unhandled exception for {}", req.getRequestURI(), ex);
        ProblemResponse body = new ProblemResponse(
                "about:blank", "Internal error", 500, "An unexpected error occurred.", req.getRequestURI());
        return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(body);
    }
}
