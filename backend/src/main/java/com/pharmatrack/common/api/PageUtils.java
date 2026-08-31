package com.pharmatrack.common.api;

/**
 * Spring Data helpers for mapping OpenAPI page/size query params into
 * {@link org.springframework.data.domain.Pageable} instances with a hard cap.
 */
public final class PageUtils {

    public static final int DEFAULT_PAGE = 0;
    public static final int DEFAULT_SIZE = 20;
    public static final int MAX_SIZE = 100;

    private PageUtils() {
    }

    public static int clampPage(Integer page) {
        return page == null || page < 0 ? DEFAULT_PAGE : page;
    }

    public static int clampSize(Integer size) {
        if (size == null || size < 1) {
            return DEFAULT_SIZE;
        }
        return Math.min(size, MAX_SIZE);
    }
}
