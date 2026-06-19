import React from 'react'
import './bootstrap'

import { createInertiaApp, Head, Link } from '@inertiajs/react'
import { createRoot } from 'react-dom/client';
// import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import WebLayout from '@/Layouts/WebLayout';

createInertiaApp({
    title: (title) => title ? `${title} - Money Exchange` : 'Laravel inertia React',
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.jsx', { eager: true });
        let page = pages[`./Pages/${name}.jsx`];
        page.default.layout = page.default.layout || ((page) => <WebLayout children={page}/>);
        return page;
    },
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />)
    },
    progress: {
    // The delay after which the progress bar will appear, in milliseconds...
    delay: 250,
    // The color of the progress bar...
    color: '#9B59B6',
    // Whether to include the default NProgress styles...
    includeCSS: true,
    // Whether the NProgress spinner will be shown...
    showSpinner: true,
  },
})