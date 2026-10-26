# База данных

Схема собрана из `database/migrations`. Локальный пример окружения использует SQLite (`DB_CONNECTION=sqlite`, файл по умолчанию `database/database.sqlite`). Часть миграции `telegram_users` ветвится по драйверу. Фактическое состояние боевой или общей с ботом базы не проверялось.

Служебные таблицы Laravel, не домен: `users`, `password_reset_tokens`, `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`. Сессии, кэш и очередь по умолчанию лежат в БД.

## objects

Назначение: объект недвижимости.

| Поле | Тип в миграции | Ограничения и смысл |
| --- | --- | --- |
| `id` | bigint PK | |
| `name` | text | Обязательное. При создании из панели — «Новый объект» |
| `description` | text, null | |
| `user_id` | FK `users.id`, null | `cascadeOnUpdate`, `cascadeOnDelete`. Индекс |
| `order` | decimal(30, 10) | При создании из панели всегда `1` |
| `notification_date` | timestamp, null | Расписание напоминаний. Комментарий миграции: «временная метка уведомления». Каст трактует значение как настенные часы |
| `user_code` | text, null | Миграция: «код пользователя в Телеграм». PHP не читает и не пишет |
| `bot_properties` | text, null | Миграция: «JSON свойств для телеграм-бота». PHP не читает и не пишет |
| `created_at`, `updated_at`, `deleted_at` | timestamp, null | `deleted_at` не используется моделью |

Удаление пользователя каскадом удаляет его объекты.

## object_properties

Назначение: счётчик объекта. В комментариях миграции у `code` и `description` текст «описание подписчика» — это расходится с моделью и UI, где `code` является кодом счётчика.

| Поле | Тип | Ограничения |
| --- | --- | --- |
| `id` | bigint PK | |
| `name` | text | |
| `code` | text, null | В валидации создания и обновления поле обязательное |
| `description` | text, null | В формах создания и обновления не принимается |
| `value` | text, null | Последнее показание строкой. Пишет `PropertyReadingService` |
| `object_id` | FK `objects.id`, null | cascade update/delete, индекс |
| `order` | decimal(30, 10) | При создании `1` |
| timestamps, `deleted_at` | timestamp, null | `deleted_at` моделью не используется |

## property_readings

Назначение: история показаний. Миграция переносит уже заполненные `object_properties.value` в первую строку истории.

| Поле | Тип | Ограничения |
| --- | --- | --- |
| `id` | bigint PK | |
| `object_property_id` | FK `object_properties.id` | cascade update/delete |
| `value` | decimal(20, 4) | В модели каст `decimal:4` |
| `recorded_at` | timestamp | Индекс вместе с `object_property_id` |
| `created_at`, `updated_at` | timestamps | |

Последнее показание для карточки: связь `latestOfMany('recorded_at')`. Пересчёт `value` сортирует по `recorded_at desc`, затем `id desc`.

## object_property_files

Назначение: вложение счётчика. Если заполнен `property_reading_id`, это фото конкретного показания.

| Поле | Тип | Ограничения |
| --- | --- | --- |
| `id` | bigint PK | |
| `object_property_id` | FK `object_properties.id` | cascade, индекс |
| `property_reading_id` | FK `property_readings.id`, null | cascade, уникальный индекс |
| `disk` | string(32), default `public` | |
| `path` | string | Путь на диске |
| `original_name` | string | |
| `mime_type` | string(127), null | |
| `size` | unsigned bigint | Байты |
| timestamps | | |

Уникальность `property_reading_id` разрешает несколько файлов с `null` и не больше одного фото на показание. Сервис API перед вставкой удаляет прежние файлы этого показания.

## subscribe_users

Назначение: подписчик объекта, получатель напоминаний бота.

| Поле | Тип | Ограничения |
| --- | --- | --- |
| `id` | bigint PK | |
| `name` | text | Username без `@` |
| `description` | text, null | Формы панели не заполняют |
| `object_id` | FK `objects.id`, null | cascade, индекс |
| `telegram_user_id` | FK `telegram_users.id`, null | `nullOnDelete` |
| `order` | decimal(30, 10) | При создании `1` |
| timestamps, `deleted_at` | | `deleted_at` моделью не используется |

## telegram_users

Назначение: слот регистрации Telegram. Модель без `updated_at` (`timestamps = false`).

| Поле | Тип | Ограничения |
| --- | --- | --- |
| `id` | bigint PK | |
| `telegram_id` | unsigned bigint, null | Уникальность. На SQLite — частичный уникальный индекс `WHERE telegram_id IS NOT NULL`. На других драйверах миграция делает колонку nullable; исходный `unique()` из первой миграции остаётся. Поведение нескольких NULL на MySQL этой сессией не проверялось |
| `first_name` | string, null | PHP при создании слота не заполняет |
| `username` | string, null | Появляется как `telegramUsername` в панели, если записан в строке |
| `registration_key` | string, null | Уникального индекса нет. Уникальность держит валидация и цикл генерации |
| `state` | string, default `main_menu` | PHP ставит `main_menu` и больше не меняет |
| `is_admin` | boolean, default false | PHP при создании подписчика пишет `false` |
| `created_at` | timestamp, null | |

`TelegramUser::isRegistered()` истинно, когда `telegram_id !== null`.

Связь с подписчиком один к одному: `subscribe_users.telegram_user_id`.

## object_finance_entries

Назначение: доход или расход. Ограничения `type` на уровне БД нет, только валидация `income` и `expense`.

| Поле | Тип | Ограничения |
| --- | --- | --- |
| `id` | bigint PK | |
| `object_id` | FK `objects.id` | cascade. Индекс `(object_id, year, month)` |
| `year` | unsigned smallint | Валидация 2000–2100 |
| `month` | unsigned tinyint | Валидация 1–12 |
| `day` | unsigned tinyint, default 1 | Валидация `checkdate` |
| `type` | string(16) | `income` или `expense` в запросе панели |
| `amount` | decimal(14, 2) | В запросе `min:0.01` |
| `comment` | string(500), null | |
| timestamps | | |

Сводка на дашборде суммирует все записи объекта, без фильтра периода. Баланс = доходы − расходы. Любой `type`, кроме `income`, попадает в расходы.

## reading_reminder_logs

Назначение по комментарию миграции: лог отправленных Telegram-напоминаний. Вставок из PHP нет.

| Поле | Тип | Ограничения |
| --- | --- | --- |
| `id` | bigint PK | |
| `object_id` | FK `objects.id` | cascade |
| `subscribe_user_id` | FK `subscribe_users.id` | cascade |
| `period_year` | unsigned smallint | |
| `period_month` | unsigned tinyint | |
| `reminder_type` | string(16) | В модели константы `due` и `overdue`. Проверка в БД не задана |
| `schedule_day`, `schedule_hour`, `schedule_minute` | unsigned tinyint | Добавлены позже. Миграция перед добавлением удаляет все существующие строки |
| `sent_at` | timestamp | |
| timestamps | | |

Уникальный ключ: объект, подписчик, период, тип, день, час и минута расписания.

При смене `objects.notification_date` модель удаляет все строки журнала этого `object_id`.

## Что приложение пишет для бота

Из PHP, при действиях панели:

- `objects`: имя, описание, владелец, расписание.
- `object_properties`: имя, код, `order`, а также `value` после показаний.
- `subscribe_users`: имя, объект, ссылка на слот.
- `telegram_users`: новый слот с ключом, `state = main_menu`, `is_admin = false`, `telegram_id = null`.
- `property_readings` и файлы — если показание или файл созданы в панели.
- удаление строк `reading_reminder_logs` при смене расписания.

Поля `user_code`, `bot_properties`, заполнение `telegram_id` / `username` / `first_name` / `state` после регистрации и вставки в `reading_reminder_logs` PHP не выполняет.

## Что приложение принимает и может хранить как данные бота

Подтверждено кодом приёма, без подтверждения, что вызывает именно Go:

- `POST /api/meters/`: новая строка `property_readings` и обновление `object_properties.value`.
- `POST /api/meters/{readingId}/photo`: строка `object_property_files` с `property_reading_id` и файл на диске `public`.

Ожидается по схеме и комментариям, но запись со стороны бота в этом репозитории не видна:

- идентификация Telegram в `telegram_users`;
- журнал напоминаний;
- `objects.user_code` и `objects.bot_properties`.

## Правила изменения критичных данных

- Ключ регистрации зарегистрированного слота (`telegram_id` не null) сервис не меняет и отвечает 422.
- Удаление подписчика через `SubscribeUserService::delete` удаляет и связанный `telegram_users`. Удаление объекта каскадом удаляет подписчиков, но не слоты Telegram.
- Удаление показания удаляет связанное фото (связь `photo` и событие `deleting` у файла) и заново выставляет `object_properties.value`.
- Повторная загрузка фото на то же показание удаляет предыдущие файлы этого `property_reading_id`.
- `app:reset` уничтожает все данные миграцией `migrate:fresh`.
