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
