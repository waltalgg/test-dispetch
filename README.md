# API DaData

## Запуск

```bash
cp .env.example .env
docker-compose up -d --build
docker-compose exec php composer install
docker-compose exec php php bin/console doctrine:migrations:migrate --no-interaction
```

## Настройка DaData

В `.env` нужно заполнить:

```dotenv
DADATA_API_KEY=
DADATA_SECRET=
```

## Проверка API

```bash
curl -X POST http://localhost:8080/api/inn/check \
  -H 'Content-Type: application/json' \
  -d '{"inn":"7707083893"}'
```
