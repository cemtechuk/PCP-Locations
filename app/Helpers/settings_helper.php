<?php

if (! function_exists('setting')) {
    /**
     * Retrieve a site setting by key.
     * Results are cached for the duration of the request.
     */
    function setting(string $key, string $default = ''): string
    {
        static $cache = null;

        if ($cache === null) {
            $cache = (new \App\Models\SettingsModel())->getAllKeyed();
        }

        return (string) ($cache[$key] ?? $default);
    }
}
