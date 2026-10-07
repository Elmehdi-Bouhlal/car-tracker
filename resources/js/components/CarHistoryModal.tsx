import { useEffect } from 'react';

import { useCarHistory } from '../composables/useCarHistory';
import type { CarHistoryModalProps } from '../types/car';
import { formatDateTime, formatField, formatValue } from '../utils/formatters';
import Loading from './Loading';

export default function CarHistoryModal({
    ident,
    onClose,
}: CarHistoryModalProps) {
    const { history, loading, error, loadHistory } = useCarHistory(ident);

    useEffect(() => {
        const closeOnEscape = (event: KeyboardEvent) => {
            if (event.key === 'Escape') {
                onClose();
            }
        };

        document.addEventListener('keydown', closeOnEscape);

        return () => document.removeEventListener('keydown', closeOnEscape);
    }, [onClose]);

    return (
        <div
            className="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4"
            role="presentation"
            onClick={onClose}
        >
            <section
                className="flex max-h-[90vh] w-full max-w-6xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl"
                role="dialog"
                aria-modal="true"
                aria-labelledby="car-history-title"
                onClick={(event) => event.stopPropagation()}
            >
                <header className="flex items-start justify-between border-b border-slate-200 px-6 py-4">
                    <div>
                        <h2
                            id="car-history-title"
                            className="text-xl font-semibold text-slate-900"
                        >
                            Car update history
                        </h2>
                        <p className="mt-1 text-sm text-slate-500">
                            Identifier: {ident}
                        </p>
                    </div>

                    <button
                        type="button"
                        className="rounded-lg px-3 py-1.5 text-xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-900"
                        aria-label="Close car history"
                        onClick={onClose}
                    >
                        ×
                    </button>
                </header>

                <div className="min-h-48 overflow-auto">
                    {loading && <Loading message="Loading car history..." />}

                    {!loading && error && (
                        <div className="p-6 text-center">
                            <p className="text-sm text-red-600">{error}</p>
                            <button
                                type="button"
                                className="mt-4 rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700"
                                onClick={() => void loadHistory()}
                            >
                                Try again
                            </button>
                        </div>
                    )}

                    {!loading && !error && history?.data.length === 0 && (
                        <p className="p-8 text-center text-sm text-slate-500">
                            This car does not have any update history yet.
                        </p>
                    )}

                    {!loading &&
                        !error &&
                        history &&
                        history.data.length > 0 && (
                            <table className="w-full min-w-4xl border-collapse text-left text-sm">
                                <thead className="sticky top-0 bg-slate-50 text-xs tracking-wide text-slate-500 uppercase">
                                    <tr>
                                        <th className="px-5 py-3 font-medium">
                                            Date
                                        </th>
                                        <th className="px-5 py-3 font-medium">
                                            Changes
                                        </th>
                                        <th className="px-5 py-3 font-medium">
                                            Latitude
                                        </th>
                                        <th className="px-5 py-3 font-medium">
                                            Longitude
                                        </th>
                                        <th className="px-5 py-3 font-medium">
                                            Speed
                                        </th>
                                        <th className="px-5 py-3 font-medium">
                                            Mileage
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100">
                                    {history.data.map((log) => (
                                        <tr
                                            key={log.id}
                                            className="hover:bg-slate-50"
                                        >
                                            <td className="px-5 py-4 whitespace-nowrap text-slate-600">
                                                {formatDateTime(log.created_at)}
                                            </td>
                                            <td className="px-5 py-4">
                                                <div className="flex max-w-md flex-wrap gap-1.5">
                                                    {Object.entries(
                                                        log.changes,
                                                    ).map(([field, value]) => (
                                                        <span
                                                            key={field}
                                                            className="rounded-md bg-blue-50 px-2 py-1 text-xs text-blue-700"
                                                        >
                                                            {formatField(field)}
                                                            :{' '}
                                                            {formatValue(value)}
                                                        </span>
                                                    ))}
                                                </div>
                                            </td>
                                            <td className="px-5 py-4 text-slate-700">
                                                {log.snapshot.position_latitude}
                                            </td>
                                            <td className="px-5 py-4 text-slate-700">
                                                {
                                                    log.snapshot
                                                        .position_longitude
                                                }
                                            </td>
                                            <td className="px-5 py-4 text-slate-700">
                                                {log.snapshot.position_speed}
                                            </td>
                                            <td className="px-5 py-4 text-slate-700">
                                                {log.snapshot.vehicle_mileage}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        )}
                </div>

                {history && history.last_page > 1 && (
                    <footer className="flex items-center justify-between border-t border-slate-200 px-6 py-4">
                        <p className="text-sm text-slate-500">
                            Page {history.current_page} of {history.last_page} ·{' '}
                            {history.total} updates
                        </p>
                        <div className="flex gap-2">
                            <button
                                type="button"
                                className="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 disabled:cursor-not-allowed disabled:opacity-40"
                                disabled={history.current_page === 1 || loading}
                                onClick={() =>
                                    void loadHistory(history.current_page - 1)
                                }
                            >
                                Previous
                            </button>
                            <button
                                type="button"
                                className="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 disabled:cursor-not-allowed disabled:opacity-40"
                                disabled={
                                    history.current_page ===
                                        history.last_page || loading
                                }
                                onClick={() =>
                                    void loadHistory(history.current_page + 1)
                                }
                            >
                                Next
                            </button>
                        </div>
                    </footer>
                )}
            </section>
        </div>
    );
}
