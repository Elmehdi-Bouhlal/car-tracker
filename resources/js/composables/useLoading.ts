import { useCallback, useState } from 'react';

export function useLoading(initialLoading = false) {
    const [loading, setLoading] = useState(initialLoading);

    const startLoading = useCallback(() => {
        setLoading(true);
    }, []);

    const stopLoading = useCallback(() => {
        setLoading(false);
    }, []);

    const toggleLoading = useCallback(() => {
        setLoading((currentLoading) => !currentLoading);
    }, []);

    return {
        loading,
        startLoading,
        stopLoading,
        toggleLoading,
    };
}
