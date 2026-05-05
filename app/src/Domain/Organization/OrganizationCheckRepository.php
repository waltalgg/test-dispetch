<?php

declare(strict_types=1);

namespace App\Domain\Organization;

interface OrganizationCheckRepository
{
    public function save(CheckedOrganization $organization): int;
}
