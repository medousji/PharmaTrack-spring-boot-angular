package com.pharmatrack.common.error;

public class ForbiddenException extends ApiException {

    public ForbiddenException(String detail) {
        super("Forbidden", 403, detail);
    }
}
