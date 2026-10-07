import { Head } from '@inertiajs/react';
import CarMap from '../components/CarMap';
import { useCarPositionUpdates } from '../composables/useCarPositionUpdates';
import type { IndexPageProps } from '../types/page';

export default function Index({ cars }: IndexPageProps) {
    const trackedCars = useCarPositionUpdates(cars);

    return (
        <>
            <Head title="Vehicle Map" />
            <main className="min-h-screen bg-slate-100 p-4 sm:p-6 lg:p-8">
                <section className="mx-auto w-full max-w-7xl">
                    <header className="mb-6">
                        <p className="text-sm font-medium tracking-wide text-blue-600 uppercase">
                            Fleet tracking
                        </p>
                        <h1 className="mt-1 text-3xl font-semibold text-slate-900">
                            Vehicle Map
                        </h1>
                        <p className="mt-2 text-slate-600">
                            View the latest reported vehicle position.
                        </p>
                    </header>

                    <CarMap cars={trackedCars} />
                </section>
            </main>
        </>
    );
}
