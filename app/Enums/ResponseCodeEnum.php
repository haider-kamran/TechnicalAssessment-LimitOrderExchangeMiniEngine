<?php

namespace App\Enums;

enum ResponseCodeEnum: int
{
    case SUCCESS = 200;
    case CREATED = 201;
    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;
    case INTERNAL_SERVER_ERROR = 500;

    public function message(): string
    {
        return match ($this) {
            self::SUCCESS => 'Success',
            self::CREATED => 'Resource created successfully',
            self::BAD_REQUEST => 'Bad request',
            self::UNAUTHORIZED => 'Unauthorized access',
            self::FORBIDDEN => 'Forbidden',
            self::NOT_FOUND => 'Resource not found',
            self::INTERNAL_SERVER_ERROR => 'Internal server error',
        };
    }
}
