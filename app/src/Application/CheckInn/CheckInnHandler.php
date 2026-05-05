<?php

declare(strict_types=1);

namespace App\Application\CheckInn;

use App\Domain\Organization\CheckedOrganization;
use App\Domain\Organization\Inn;
use App\Domain\Organization\OrganizationCheckRepository;
use App\Domain\Organization\OrganizationLookupGateway;

/**
 * Обработчик команды проверки ИНН. Получает ИНН, запрашивает данные организации по этому ИНН, сохраняет результат проверки и возвращает его.
 */
final readonly class CheckInnHandler
{
    public function __construct(
        private OrganizationLookupGateway $lookupGateway,
        private OrganizationCheckRepository $checks,
    ) {
    }

    public function __invoke(string $inn): CheckInnResult
    {
        $normalizedInn = Inn::fromString($inn);
        $lookup = $this->lookupGateway->findByInn($normalizedInn);
        $checkedOrganization = CheckedOrganization::fromLookup($normalizedInn, $lookup);

        $id = $this->checks->save($checkedOrganization);

        return CheckInnResult::fromCheckedOrganization($id, $checkedOrganization);
    }
}
