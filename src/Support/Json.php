<?php

declare(strict_types=1);

namespace Portal\Support;

use RuntimeException;

final class Json
{
    public static function decode(string $json, mixed $default = []): mixed
    {
        $decoded = json_decode($json, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : $default;
    }

    public static function encode(mixed $value): string
    {
        $json = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        if ($json === false) {
            throw new RuntimeException('JSON encoding failed.');
        }

        return $json;
    }
}
