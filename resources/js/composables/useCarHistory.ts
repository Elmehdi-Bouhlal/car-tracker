import { useCallback, useEffect, useState } from 'react';

import { carService } from '../services/carService';
import type { PaginatedResponse } from '../types/api';
import type { CarLog } from '../types/car';
import { getErrorMessage } from '../utils/errors';
import { useLoading } from './useLoading';

export function useCarHistory(ident: string) {
    const [history, setHistory] = useState<PaginatedResponse<CarLog> | null>(
        null,
    );
    const [error, setError] = useState<string | null>(null);
    const { loading, startLoading, stopLoading } = useLoading(true);

    const loadHistory = useCallback(
        async (page = 1): Promise<void> => {
            startLoading();
            setError(null);

            try {
                setHistory(await carService.getHistory(ident, page));
            } catch (caughtError) {
                setError(
                    getErrorMessage(
                        caughtError,
                        'Problem while loading the car history.',
                    ),
                );
            } finally {
                stopLoading();
            }
        },
        [ident, startLoading, stopLoading],
    );

    useEffect(() => {
        setHistory(null);
        void loadHistory();
    }, [loadHistory]);

    return {
        history,
        loading,
        error,
        loadHistory,
    };
}
