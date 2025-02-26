import './bootstrap';
import { createInertiaApp } from '@inertiajs/react'
import { createRoot } from 'react-dom/client'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { InertiaProgress } from '@inertiajs/progress'

// Inicializa a barra de progresso
InertiaProgress.init({
    color: '#4B5563',
    showSpinner: true,
});

createInertiaApp({
    title: (title) => `${title} - Super Publisher`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.jsx`, import.meta.glob('./Pages/**/*.jsx')),
    setup({ el, App, props }) {
        const root = createRoot(el);
        root.render(<App {...props} />);
    },
}).then(() => {
    
});