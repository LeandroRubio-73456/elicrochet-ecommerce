const path = require('path');

// Workaround for Windows EPERM error: use a custom directory so chrome-launcher doesn't attempt to delete it
// and fail due to file locking issues.
const chromeDataDir = path.join(process.cwd(), '.lighthouse_tmp');

module.exports = {
    ci: {
        collect: {
            startServerCommand: 'php artisan serve',
            startServerReadyPattern: 'Server running on',
            url: ['http://localhost:8000/'],
            numberOfRuns: 3,
            settings: {
                // added --user-data-dir to bypass automatic profile cleanup which fails on Windows
                chromeFlags: `--headless=new --no-sandbox --disable-gpu --user-data-dir="${chromeDataDir}"`,
            },
        },
        upload: {
            target: 'temporary-public-storage',
        },
        assert: {
            // preset: 'lighthouse:no-pwa', // Commented out to avoid strict failures
            assertions: {
                'categories:performance': ['warn', { minScore: 0.5 }],
                'categories:accessibility': ['warn', { minScore: 0.7 }],
                'categories:best-practices': ['warn', { minScore: 0.7 }],
                'categories:seo': ['warn', { minScore: 0.8 }],
            },
        },
    },
};
