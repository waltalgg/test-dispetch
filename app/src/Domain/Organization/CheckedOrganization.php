<?php

declare(strict_types=1);

namespace App\Domain\Organization;

final readonly class CheckedOrganization
{
    public function __construct(
        public Inn $inn,
        public string $name,
        public bool $active,
        public ?string $okved,
        public array $rawResponse,
        public \DateTimeImmutable $checkedAt,
    ) {
    }

    public static function fromLookup(Inn $inn, OrganizationLookupResult $lookup): self
    {
        return new self(
            $inn,
            $lookup->name,
            $lookup->active,
            $lookup->okved,
            $lookup->rawResponse,
            new \DateTimeImmutable(),
        );
    }
}
