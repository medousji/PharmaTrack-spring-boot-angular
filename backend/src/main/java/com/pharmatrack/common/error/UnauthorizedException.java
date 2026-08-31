package com.pharmatrack.common.error;

public class UnauthorizedException extends ApiException {

    public UnauthorizedException(String detail) {
        super("Unauthorized", 401, detail);
    }
}
