<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine;

use App\Domain\Organization\CheckedOrganization;
use App\Domain\Organization\OrganizationCheckRepository;
use App\Infrastructure\Persistence\Doctrine\Entity\OrganizationCheckRecord;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineOrganizationCheckRepository implements OrganizationCheckRepository
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function save(CheckedOrganization $organization): int
    {
        $record = OrganizationCheckRecord::fromCheckedOrganization($organization);

        $this->entityManager->persist($record);
        $this->entityManager->flush();

        return $record->id();
    }
}
