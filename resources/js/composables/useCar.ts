import { useCallback, useState } from 'react';

import { carService } from '../services/carService';
import type {
    Car,
    CarOperation,
    CreateCarPayload,
    UpdateCarPayload,
} from '../types/car';
import { getErrorMessage } from '../utils/errors';

export function useCar() {
    const [car, setCar] = useState<Car | null>(null);
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const execute = useCallback(
        async (operation: CarOperation): Promise<Car | null> => {
            setIsLoading(true);
            setError(null);

            try {
                const result = await operation();
                setCar(result);

                return result;
            } catch (caughtError) {
                setError(
                    getErrorMessage(
                        caughtError,
                        'Problem while handling the request.',
                    ),
                );

                return null;
            } finally {
                setIsLoading(false);
            }
        },
        [],
    );

    const createCar = useCallback(
        (payload: CreateCarPayload) =>
            execute(() => carService.create(payload)),
        [execute],
    );

    const findCar = useCallback(
        (ident: string) => execute(() => carService.findByIdent(ident)),
        [execute],
    );

    const updateCar = useCallback(
        (ident: string, payload: UpdateCarPayload) =>
            execute(() => carService.updateByIdent(ident, payload)),
        [execute],
    );

    const clearCar = useCallback(() => {
        setCar(null);
        setError(null);
    }, []);

    return {
        car,
        isLoading,
        error,
        createCar,
        findCar,
        updateCar,
        clearCar,
    };
}
