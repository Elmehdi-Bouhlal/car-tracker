export function formatValue(value: unknown): string {
    if (typeof value === 'boolean') {
        return value ? 'Yes' : 'No';
    }

    if (value === null || value === undefined) {
        return '—';
    }

    if (typeof value === 'string') {
        return value;
    }

    if (typeof value === 'number') {
        return value.toString();
    }

    return JSON.stringify(value) ?? '—';
}

export function formatField(field: string): string {
    return field.replaceAll('_', ' ');
}

export function formatDateTime(value: string): string {
    const date = new Date(value);

    return Number.isNaN(date.getTime()) ? '—' : date.toLocaleString();
}
