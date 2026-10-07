import mapWorkerUrl from 'maplibre-gl/dist/maplibre-gl-worker.mjs?url';

export const mapConfig = {
    styleUrl: 'https://demotiles.maplibre.org/style.json',
    workerUrl: mapWorkerUrl,
    defaultPosition: {
        latitude: 43.955218,
        longitude: 37.661918,
    },
    defaultZoom: 13,
    transitionDuration: 800,
    boundsPadding: 80,
    maxBoundsZoom: 16,
} as const;
