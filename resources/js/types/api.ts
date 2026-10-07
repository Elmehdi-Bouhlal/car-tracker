export type SuccessResponse<T> = {
    success: true;
    data: T;
};

export type ErrorResponse = {
    success: false;
    error: string;
};

export type ApiResponse<T> = SuccessResponse<T> | ErrorResponse;

export type PaginatedResponse<T> = {
    current_page: number;
    data: T[];
    from: number | null;
    last_page: number;
    next_page_url: string | null;
    per_page: number;
    prev_page_url: string | null;
    to: number | null;
    total: number;
};
