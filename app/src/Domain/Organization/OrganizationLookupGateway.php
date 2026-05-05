<?php

declare(strict_types=1);

namespace App\Domain\Organization;

interface OrganizationLookupGateway
{
    public function findByInn(Inn $inn): OrganizationLookupResult;
}
