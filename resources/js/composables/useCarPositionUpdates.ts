import { useEchoPublic } from '@laravel/echo-react';
import { useState } from 'react';

import type { Car, CarPositionEventPayload } from '../types/car';

export function useCarPositionUpdates(initialCars: Car[] = []) {
    const [cars, setCars] = useState<Car[]>(() => [
        ...new Map(initialCars.map((car) => [car.ident, car])).values(),
    ]);

    useEchoPublic<CarPositionEventPayload>(
        'cars.positions',
        '.car.position.updated',
        (event) => {
            setCars((currentCars) => {
                const carExists = currentCars.some(
                    (car) => car.ident === event.car.ident,
                );

                if (!carExists) {
                    return [...currentCars, event.car];
                }

                return currentCars.map((car) =>
                    car.ident === event.car.ident ? event.car : car,
                );
            });

            console.info('[Reverb] Car position updated:', event);
        },
        [],
    );

    return cars;
}
