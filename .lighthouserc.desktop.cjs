const path = require('path');

// Workaround for Windows EPERM error
const chromeDataDir = path.join(process.cwd(), '.lighthouse_tmp_desktop');

module.exports = {
    ci: {
        collect: {
            startServerCommand: 'php artisan serve',
            startServerReadyPattern: 'Server running on',
            url: ['http://localhost:8000/'],
            numberOfRuns: 1,
            settings: {
                formFactor: 'desktop',
                screenEmulation: {
                    mobile: false,
                },
                // added --user-data-dir to bypass automatic profile cleanup which fails on Windows
                chromeFlags: `--headless=new --no-sandbox --disable-gpu --disable-dev-shm-usage --user-data-dir="${chromeDataDir}"`,
            },
        },
        upload: {
            target: 'temporary-public-storage',
        },
        assert: {
            // preset: 'lighthouse:no-pwa',
            assertions: {
                'categories:performance': ['warn', { minScore: 0.5 }],
                'categories:accessibility': ['warn', { minScore: 0.7 }],
                'categories:best-practices': ['warn', { minScore: 0.7 }],
                'categories:seo': ['warn', { minScore: 0.8 }],
            },
        },
    },
};
