<?php

namespace Config;

final class RateLimitConfig
{
    public const DEFAULTS = [
        'max_attempts'        => 5,
        'window_minutes'      => 60,
        'soft_throttle_after' => 3,
    ];

    public const USER_CREATE = [
        'max_attempts'        => 5,
        'window_minutes'      => 60,
        'soft_throttle_after' => 3,
    ];

    public const CONTACT_SEND = [
        'max_attempts'        => 3,
        'window_minutes'      => 30,
        'soft_throttle_after' => 2,
    ];

    public const AUTH_LOGIN = [
        'max_attempts'        => 10,
        'window_minutes'      => 15,
        'soft_throttle_after' => 5,
    ];
}
