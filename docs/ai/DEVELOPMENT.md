# Разработка

Команды ниже взяты из `composer.json`, `package.json`, `.env.example`, `phpunit.xml` и `.github/workflows`. Успешный полный прогон тестов в сессии фиксации документации не выполнялся.

## Окружение

- PHP по `composer.json`: `^8.2`. CI (`.github/workflows/tests.yml` и `lint.yml`) ставит PHP 8.4 и Node 22.
- Зафиксированный фреймворк в `composer.lock`: Laravel `v12.20.0`.
- Расширения, которые реально нужны домену: `pdo`, драйвер выбранной БД, `mbstring`, `openssl`, `tokenizer`, `xml`, `fileinfo` (загрузки), `gd` или совместимый набор для PhpSpreadsheet. Полный список расширений на машине в этой сессии не снимался.
- Локальная БД по примеру: SQLite, файл `database/database.sqlite`. Очередь, сессии и кэш тоже в этой БД, поэтому миграции обязательны до `composer run dev`.
- Часовой пояс по умолчанию: `Europe/Moscow` (`config/app.php`, ключ `APP_TIMEZONE`).
- `laravel/sail` есть в `require-dev`. Файла `docker-compose` в репозитории нет, сценарий Sail не настроен.
- Скрипт `composer cs-fix` вызывает `php-cs-fixer`, пакета в `composer.json` нет. В CI стиль PHP проверяет Laravel Pint.

На PHP 8.5 константа `PDO::MYSQL_ATTR_SSL_CA` объявлена устаревшей. В `config/database.php` для mysql и mariadb при PHP ≥ 8.5 берётся `Pdo\Mysql::ATTR_SSL_CA`. Laravel 12.20.0 при загрузке всё равно подключает свой `vendor/laravel/framework/config/database.php`. В рабочей копии этот vendor-файл может быть поправлен локально; в git он не входит. На чистой установке 12.20 предупреждение при старте на PHP 8.5 возвращается. Исправление во фреймворке есть в более новых релизах 12.x; обновление фреймворка в эту фиксацию не входит.

## Установка

```bash
composer install
npm ci
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan storage:link
```

`storage:link` нужен, если файлы открываются по публичному `/storage/...`. Скачивание через маршрут `property.files.show` идёт через `Storage::disk`, симлинк для этого маршрута не обязателен. Команда `publicStorageUrl()` у модели симлинк предполагает.

`composer install` сам по себе `.env` и sqlite не создаёт: скрипты `post-root-package-install` и `post-create-project-cmd` рассчитаны на `composer create-project`.

Секреты, ключи и пароли в документацию не копируются. Имена переменных из `.env.example`: `APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_DEBUG`, `APP_URL`, `APP_TIMEZONE`, `APP_LOCALE`, `APP_FALLBACK_LOCALE`, `APP_FAKER_LOCALE`, `APP_MAINTENANCE_DRIVER`, `PHP_CLI_SERVER_WORKERS`, `BCRYPT_ROUNDS`, `LOG_*`, `DB_CONNECTION` и закомментированные `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `SESSION_*`, `BROADCAST_CONNECTION`, `FILESYSTEM_DISK`, `QUEUE_CONNECTION`, `CACHE_STORE`, `MEMCACHED_HOST`, `REDIS_*`, `MAIL_*`, `AWS_*`, `VITE_APP_NAME`.

`GO_API_OBJECT_POST_URL` в примере окружения отсутствует. Добавлять её при общей базе с ботом нельзя: так написано в `config/services.php`.

## Локальный запуск

```bash
composer run dev
```

Поднимает четыре процесса: `php artisan serve` с лимитами загрузки 12M, `php artisan queue:listen --tries=1`, `php artisan pail`, `npm run dev`.

SSR-вариант: `composer run dev:ssr` (`npm run build:ssr` и `php artisan inertia:start-ssr`). Обычная разработка идёт без SSR.

Vite в режиме разработки слушает свой порт и проксируется плагином Laravel. `APP_URL` должен быть задан, иначе плагин пишет `APP_URL: undefined`.

Приложение после миграций пустое, пока пользователь не зарегистрируется на `/register`. Объекты дашборда принадлежат этому пользователю.

## Миграции и сидеры

```bash
php artisan migrate
php artisan migrate:fresh
php artisan db:seed --class=TelegramUserSeeder
php artisan app:reset --seed-telegram
```

`app:reset` спрашивает подтверждение, на production дополнительно печатает предупреждение, но отдельного запрета запуска нет. `--force` пропускает вопрос. `--keep-files` не удаляет `property-files`. Команда вызывает `migrate:fresh` и стирает все данные.

`DatabaseSeeder` вызывает `TelegramUserSeeder` и `ObjectModel::factory()` трижды с `user_id = 1`. Без пользователя с id 1 внешний ключ не выполнится. Для пустой панели этот сидер не предназначен. `app:reset` его не вызывает.

`TelegramUserSeeder` создаёт или обновляет один слот бота с `is_admin = true` и фиксированным ключом. Ключ совпадает, по комментарию сидера, с `goBot/migrations/init.sql` вне этого репозитория.

## Тесты и качество

```bash
composer test
```

Это `php artisan config:clear` и `php artisan test`. PHPUnit 11, sqlite `:memory:`, `QUEUE_CONNECTION=sync`, `SESSION_DRIVER=array`, `CACHE_STORE=array`.

Наборы: `tests/Unit`, `tests/Feature`. Покрыты гостевой редирект дашборда, вход на дашборд, регистрация, логин, сброс и подтверждение пароля, верификация email, обновление профиля и пароля. Доменных тестов нет. `tests/Unit/ExampleTest.php` проверяет `assertTrue(true)`.

CI на push и pull request в `develop` и `main`:

- `tests.yml`: Node 22, `npm ci`, `npm run build`, PHP 8.4, `composer install`, копия `.env`, `key:generate`, `./vendor/bin/phpunit`.
- `lint.yml`: `vendor/bin/pint`, `npm run format`, `npm run lint`. Автокоммит стиля закомментирован.

Локальные команды фронтенда:

```bash
npm run dev
npm run build
npm run build:ssr
npm run types
npm run lint
npm run format
npm run format:check
```

`npm run lint` запускает ESLint с `--fix`. Pint в CI тоже пишет изменения в файлы, если его запустить локально без `--test`. Перед проверкой чужого diff это нужно учитывать.

Отдельного phpstan/psalm конфига в корне нет. `_ide_helper.php` в репозитории присутствует.

## Сборка фронтенда

Страницы: `resources/js/pages/**/*.tsx`, вход `resources/js/app.tsx`, SSR-вход `resources/js/ssr.tsx`. Сборка описана в `vite.config.ts`, алиасы — в `tsconfig.json`, UI-конфиг — в `components.json`. Перед сменой сборки читать `vite.config.ts`, а не эту справку.

Продакшен-сборка, которую ждёт CI: `npm run build`.

## Порядок безопасных изменений

1. Прочитать `docs/ai/AI_CONTEXT.md` и профильный документ.
2. Открыть реализацию, которую собираетесь менять: маршрут, request, контроллер, сервис, модель, миграцию, страницу Inertia.
3. Если меняется таблица или JSON, с которыми живёт бот, сверить `DATABASE.md` и `API_AND_INTEGRATIONS.md`. Исходников бота в репозитории нет, совместимость с ним по одному PHP-репозиторию не доказывается.
4. Не вызывать `migrate:fresh` и `app:reset` на базе, где есть данные пользователя или бота.
5. Для проверки домена существующих тестов недостаточно. Минимальная автоматическая проверка каркаса: `php artisan test` или точечный файл из `tests/Feature`. Сборка фронтенда: `npm run types` и при изменении верстки `npm run build`.
6. После смены поведения обновить соответствующий файл в `docs/ai/`.
