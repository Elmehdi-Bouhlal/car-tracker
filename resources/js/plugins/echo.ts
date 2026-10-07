import { configureEcho } from '@laravel/echo-react';

const reverbPort = Number(import.meta.env.VITE_REVERB_PORT);

configureEcho({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: reverbPort,
    wssPort: reverbPort,
    forceTLS: import.meta.env.VITE_REVERB_SCHEME === 'https',
    enabledTransports: ['ws', 'wss'],
});
