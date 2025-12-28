module.exports = {
    ci: {
        collect: {
            startServerCommand: 'php artisan serve',
            startServerReadyPattern: 'Server running on',
            url: ['http://localhost:8000/'],
            numberOfRuns: 3,
        },
        upload: {
            target: 'temporary-public-storage',
        },
        assert: {
            preset: 'lighthouse:no-pwa',
            assertions: {
                // Example assertions (can be customized)
                'categories:performance': ['warn', { minScore: 0.8 }],
                'categories:accessibility': ['warn', { minScore: 0.9 }],
            },
        },
    },
};
