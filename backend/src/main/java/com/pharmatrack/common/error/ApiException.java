package com.pharmatrack.common.error;

/**
 * Base exception mapped to an RFC 7807 problem+json response.
 * Subclasses set the {@code title} and {@code status} used by
 * {@link GlobalExceptionHandler}.
 */
public abstract class ApiException extends RuntimeException {

    private final String title;
    private final int status;

    protected ApiException(String title, int status, String detail) {
        super(detail);
        this.title = title;
        this.status = status;
    }

    public String getTitle() {
        return title;
    }

    public int getStatus() {
        return status;
    }
}
