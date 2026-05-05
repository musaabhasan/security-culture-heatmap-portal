<?php

declare(strict_types=1);

namespace Portal\Support;

final class View
{
    public static function e(string|int|float|null $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}
