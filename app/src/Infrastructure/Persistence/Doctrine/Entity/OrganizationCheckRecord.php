<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Entity;

use App\Domain\Organization\CheckedOrganization;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'organization_checks')]
#[ORM\Index(columns: ['inn'], name: 'idx_organization_checks_inn')]
class OrganizationCheckRecord
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(length: 12)]
    private string $inn;

    #[ORM\Column(name: 'organization_name', length: 512)]
    private string $organizationName;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $active;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $okved;

    #[ORM\Column(name: 'raw_response', type: Types::JSON)]
    private array $rawResponse;

    #[ORM\Column(name: 'checked_at', type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $checkedAt;

    private function __construct()
    {
    }

    public static function fromCheckedOrganization(CheckedOrganization $organization): self
    {
        $record = new self();
        $record->inn = $organization->inn->value;
        $record->organizationName = $organization->name;
        $record->active = $organization->active;
        $record->okved = $organization->okved;
        $record->rawResponse = $organization->rawResponse;
        $record->checkedAt = $organization->checkedAt;

        return $record;
    }

    public function id(): int
    {
        if ($this->id === null) {
            throw new \LogicException('Запись проверки организации не была сохранена.');
        }

        return $this->id;
    }
}
