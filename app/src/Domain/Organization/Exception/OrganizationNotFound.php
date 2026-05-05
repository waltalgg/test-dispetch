<?php

declare(strict_types=1);

namespace App\Domain\Organization\Exception;

use App\Domain\Organization\Inn;

final class OrganizationNotFound extends \RuntimeException
{
    public static function byInn(Inn $inn): self
    {
        return new self('Организация с ИНН '.$inn->value.' не найдены.');
    }
}
