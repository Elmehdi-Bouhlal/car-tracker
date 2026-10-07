import type { ApiResponse, PaginatedResponse } from '../types/api';
import type {
    Car,
    CarLog,
    CreateCarPayload,
    UpdateCarPayload,
} from '../types/car';

export class CarApiError extends Error {
    public constructor(
        message: string,
        public readonly status: number,
    ) {
        super(message);
        this.name = 'CarApiError';
    }
}

const endpoint = '/api/truckers';

async function request<T>(url: string, init?: RequestInit): Promise<T> {
    const headers = new Headers(init?.headers);
    headers.set('Accept', 'application/json');

    if (init?.body) {
        headers.set('Content-Type', 'application/json');
    }

    const response = await fetch(url, {
        ...init,
        headers,
    });

    const result = (await response.json()) as ApiResponse<T>;

    if (!response.ok || !result.success) {
        const message = !result.success
            ? result.error
            : 'Problem while handling the request.';

        throw new CarApiError(message, response.status);
    }

    return result.data;
}

export const carService = {
    create(payload: CreateCarPayload): Promise<Car> {
        return request<Car>(endpoint, {
            method: 'POST',
            body: JSON.stringify(payload),
        });
    },

    findByIdent(ident: string): Promise<Car> {
        return request<Car>(`${endpoint}/${encodeURIComponent(ident)}`);
    },

    updateByIdent(ident: string, payload: UpdateCarPayload): Promise<Car> {
        return request<Car>(`${endpoint}/${encodeURIComponent(ident)}`, {
            method: 'PATCH',
            body: JSON.stringify(payload),
        });
    },

    getHistory(
        ident: string,
        page = 1,
        perPage = 10,
    ): Promise<PaginatedResponse<CarLog>> {
        const query = new URLSearchParams({
            page: page.toString(),
            per_page: perPage.toString(),
        });

        return request<PaginatedResponse<CarLog>>(
            `${endpoint}/${encodeURIComponent(ident)}/history?${query.toString()}`,
        );
    },
};
