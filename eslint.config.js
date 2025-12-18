import js from '@eslint/js';

export default [
    {
        ignores: ['node_modules/**', 'public/**', 'storage/**', 'vendor/**', 'bootstrap/**'],
    },
    {
        ...js.configs.recommended,
        languageOptions: {
            ...js.configs.recommended.languageOptions,
            sourceType: 'module',
            globals: {
                ...js.configs.recommended.languageOptions?.globals,
                window: 'readonly',
                document: 'readonly',
                console: 'readonly',
                DataTransfer: 'readonly',
                URLSearchParams: 'readonly',
                setTimeout: 'readonly',
                requestAnimationFrame: 'readonly',
            },
        },
        rules: {
            ...js.configs.recommended.rules,
            'no-unused-vars': ['warn', { argsIgnorePattern: '^_', varsIgnorePattern: '^_' }],
            'no-undef': 'warn',
            'no-console': 'off',
        },
    },
];
