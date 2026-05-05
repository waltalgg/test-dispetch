<?php

declare(strict_types=1);

namespace App\Application\CheckInn;

use App\Domain\Organization\CheckedOrganization;

/**
 * Результат проверки ИНН. Содержит данные организации, полученные в результате проверки, и идентификатор сохраненной проверки.
 */
final readonly class CheckInnResult
{
    public function __construct(
        public int $id,
        public string $inn,
        public string $name,
        public bool $active,
        public ?string $okved,
        public \DateTimeImmutable $checkedAt,
    ) {
    }

    public static function fromCheckedOrganization(int $id, CheckedOrganization $organization): self
    {
        return new self(
            $id,
            $organization->inn->value,
            $organization->name,
            $organization->active,
            $organization->okved,
            $organization->checkedAt,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'inn' => $this->inn,
            'name' => $this->name,
            'active' => $this->active,
            'okved' => $this->okved,
            'checkedAt' => $this->checkedAt->format(DATE_ATOM),
        ];
    }
}
