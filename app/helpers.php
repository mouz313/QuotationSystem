<?php

use App\Models\Setting;

if (!function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (!function_exists('setting_int')) {
    function setting_int(string $key, int $default = 0): int
    {
        return (int) Setting::get($key, $default);
    }
}
