<?php

declare(strict_types=1);

namespace App\Domain\Organization\Exception;

final class InvalidInn extends \InvalidArgumentException
{
    public static function becauseFormatIsWrong(): self
    {
        return new self('ИНН должен содержать 10 или 12 цифр.'); // 10 - для организаций, 12 - для ИП
    }
}
