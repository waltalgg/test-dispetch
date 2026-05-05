<?php

declare(strict_types=1);

namespace App\Domain\Organization;

final readonly class OrganizationLookupResult
{
    public function __construct(
        public string $name,
        public bool $active,
        public ?string $okved,
        public array $rawResponse,
    ) {
    }
}
