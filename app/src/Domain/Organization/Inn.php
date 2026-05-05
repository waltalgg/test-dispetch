<?php

declare(strict_types=1);

namespace App\Domain\Organization;

use App\Domain\Organization\Exception\InvalidInn;

final readonly class Inn
{
    private function __construct(public string $value)
    {
    }

    public static function fromString(string $value): self
    {
        $digits = preg_replace('/\D+/', '', $value) ?? ''; // Оставляем только цифры

        if (!in_array(strlen($digits), [10, 12], true) || !ctype_digit($digits)) { // ИНН должен содержать 10 или 12 цифр, и все должны быть цифрами
            throw InvalidInn::becauseFormatIsWrong();
        }

        return new self($digits);
    }
}
