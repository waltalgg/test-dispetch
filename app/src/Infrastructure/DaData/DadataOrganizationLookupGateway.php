<?php

declare(strict_types=1);

namespace App\Infrastructure\DaData;

use App\Domain\Organization\Exception\OrganizationLookupFailed;
use App\Domain\Organization\Exception\OrganizationNotFound;
use App\Domain\Organization\Inn;
use App\Domain\Organization\OrganizationLookupGateway;
use App\Domain\Organization\OrganizationLookupResult;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class DadataOrganizationLookupGateway implements OrganizationLookupGateway
{
    private const FIND_ORGANIZATION_PATH = '/suggestions/api/4_1/rs/findById/party'; // Путь к API для поиска организации по ИНН

    public function __construct(
        private HttpClientInterface $httpClient,
        private string $apiKey,
        private string $secret,
        private string $baseUri,
    ) {
    }
    /**
     * Ищет организацию по ИНН, используя API DaData. Выполняет POST-запрос с необходимыми заголовками и обрабатывает ответ, извлекая название, статус и код ОКВЭД организации.
     *
     * @param Inn $inn - ИНН организации для поиска
     *
     * @return OrganizationLookupResult - Результат поиска организации, содержащий название, статус активности и код ОКВЭД
     *
     * @throws OrganizationLookupFailed - Если произошла ошибка при выполнении запроса или обработки ответа от DaData
     * @throws OrganizationNotFound - Если организация с указанным ИНН не найдена в ответе DaData
     */
    public function findByInn(Inn $inn): OrganizationLookupResult
    {
        if ($this->apiKey === '') {
            throw new OrganizationLookupFailed('Не настроен API-ключ DaData.');
        }

        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Token '.$this->apiKey,
            'Content-Type' => 'application/json',
        ];

        if ($this->secret !== '') {
            $headers['X-Secret'] = $this->secret; // Если secret задан, добавляем его в заголовки
        }

        try {
            $response = $this->httpClient->request('POST', rtrim($this->baseUri, '/').self::FIND_ORGANIZATION_PATH, [
                'headers' => $headers,
                'json' => [
                    'query' => $inn->value,
                    'count' => 1,
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $payload = $response->toArray(false);
        } catch (TransportExceptionInterface|DecodingExceptionInterface $exception) {
            throw new OrganizationLookupFailed('Не удалось выполнить запрос в DaData.', previous: $exception);
        }

        if ($statusCode >= 400) {
            throw new OrganizationLookupFailed('DaData вернул HTTP '.$statusCode.'.');
        }

        $suggestion = $payload['suggestions'][0] ?? null;

        if (!is_array($suggestion)) {
            throw OrganizationNotFound::byInn($inn);
        }

        return new OrganizationLookupResult(
            $this->extractName($suggestion),
            $this->isActive($suggestion),
            $this->extractOkved($suggestion),
            $payload,
        );
    }

    /**
     * Извлекает название организации из данных предложения DaData. Пытается получить название из нескольких полей, возвращая первое непустое значение.
     *
     * @param array<string, mixed> $suggestion - Массив данных предложения DaData
     *
     * @return string - Название организации
     *
     * @throws OrganizationLookupFailed - Если название организации не найдено в ответе DaData
     */
    private function extractName(array $suggestion): string
    {
        $data = is_array($suggestion['data'] ?? null) ? $suggestion['data'] : [];
        $name = is_array($data['name'] ?? null) ? $data['name'] : [];
        $fio = is_array($data['fio'] ?? null) ? $data['fio'] : [];

        foreach ([
            $name['full_with_opf'] ?? null,
            $name['short_with_opf'] ?? null,
            $fio['source'] ?? null,
            $suggestion['value'] ?? null,
        ] as $candidate) {
            if (is_string($candidate) && $candidate !== '') {
                return $candidate;
            }
        }

        throw new OrganizationLookupFailed('Ответ DaData не содержит название организации или ФИО ИП.');
    }
    /**
     * Определяет, является ли организация активной, на основе данных предложения DaData. Проверяет наличие статуса "ACTIVE" в структуре данных.
     *
     * @param array<string, mixed> $suggestion - Массив данных предложения DaData
     *
     * @return bool - true, если организация активна, false в противном случае
     */
    private function isActive(array $suggestion): bool
    {
        $data = is_array($suggestion['data'] ?? null) ? $suggestion['data'] : [];
        $state = is_array($data['state'] ?? null) ? $data['state'] : [];

        return ($state['status'] ?? null) === 'ACTIVE';
    }

    /**
     * Извлекает код ОКВЭД из данных предложения DaData. Сначала пытается получить код из поля "okved", затем из массива "okveds", возвращая первый непустой код.
     *
     * @param array<string, mixed> $suggestion - Массив данных предложения DaData
     *
     * @return string|null - Код ОКВЭД или null, если код не найден
     */
    private function extractOkved(array $suggestion): ?string
    {
        $data = is_array($suggestion['data'] ?? null) ? $suggestion['data'] : [];
        $okved = $data['okved'] ?? null;

        if (is_string($okved) && $okved !== '') {
            return $okved;
        }

        $okveds = is_array($data['okveds'] ?? null) ? $data['okveds'] : [];
        $firstOkved = is_array($okveds[0] ?? null) ? ($okveds[0]['code'] ?? null) : null;

        return is_string($firstOkved) && $firstOkved !== '' ? $firstOkved : null;
    }
}
