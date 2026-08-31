package com.pharmatrack.common.error;

public class ConflictException extends ApiException {

    public ConflictException(String detail) {
        super("Conflict", 409, detail);
    }
}
