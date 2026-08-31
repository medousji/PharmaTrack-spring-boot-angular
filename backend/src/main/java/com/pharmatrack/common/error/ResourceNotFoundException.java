package com.pharmatrack.common.error;

public class ResourceNotFoundException extends ApiException {

    public ResourceNotFoundException(String resource, Object id) {
        super("Resource not found", 404,
                "No " + resource + " found with identifier '" + id + "'.");
    }
}
