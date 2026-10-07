import type { LoadingProps } from '../types/loading';

export default function Loading({ message = 'Loading...' }: LoadingProps) {
    return (
        <div
            className="flex items-center justify-center gap-3 p-4 text-slate-600"
            role="status"
            aria-live="polite"
        >
            <span
                className="h-5 w-5 animate-spin rounded-full border-2 border-slate-300 border-t-slate-700"
                aria-hidden="true"
            />
            <span>{message}</span>
        </div>
    );
}
