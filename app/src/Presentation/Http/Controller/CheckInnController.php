<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller;

use App\Application\CheckInn\CheckInnHandler;
use App\Domain\Organization\Exception\InvalidInn;
use App\Domain\Organization\Exception\OrganizationLookupFailed;
use App\Domain\Organization\Exception\OrganizationNotFound;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final readonly class CheckInnController
{
    public function __construct(private CheckInnHandler $checkInn)
    {
    }

    /**
     * Обрабатывает POST-запрос на проверку ИНН. Получает ИНН из JSON-тела запроса, вызывает обработчик проверки ИНН и возвращает результат в формате JSON.
     *
     * @param Request $request - HTTP-запрос, содержащий JSON с полем "inn"
     *
     * @return JsonResponse - JSON-ответ с результатом проверки или сообщением об ошибке
     */

    #[Route('/api/inn/check', name: 'api_inn_check', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $payload = json_decode($request->getContent(), true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return $this->error('Некорректное тело JSON-запроса.', JsonResponse::HTTP_BAD_REQUEST);
        }

        if (!is_array($payload) || !is_string($payload['inn'] ?? null)) {
            return $this->error('Поле "inn" обязательно.', JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            $result = ($this->checkInn)($payload['inn']);
        } catch (InvalidInn $exception) {
            return $this->error($exception->getMessage(), JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        } catch (OrganizationNotFound $exception) {
            return $this->error($exception->getMessage(), JsonResponse::HTTP_NOT_FOUND);
        } catch (OrganizationLookupFailed $exception) {
            return $this->error($exception->getMessage(), JsonResponse::HTTP_BAD_GATEWAY);
        }

        return new JsonResponse(['data' => $result->toArray()]);
    }

    private function error(string $message, int $statusCode): JsonResponse
    {
        return new JsonResponse([
            'error' => [
                'message' => $message,
            ],
        ], $statusCode);
    }
}
