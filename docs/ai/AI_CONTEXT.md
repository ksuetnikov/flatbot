# Контекст для AI-сессий

Этот файл читается первым. Он не заменяет код: после него открываются реализация и профильный документ из `docs/ai/`.

## Что это за проект

GoFlat — Laravel 12.20.0 панель учёта объектов недвижимости: счётчики, показания, файлы, доходы и расходы, подписчики Telegram. UI — Inertia React, бренд в `resources/js/lib/brand.ts`. Пользователь панели — строка `users` без ролей. Арендатор в боте — пара `subscribe_users` + `telegram_users`.

Исходников Go-бота в репозитории нет. Совместная работа задумана через общую базу. HTTP-отправка объекта в Go написана и помечена устаревшей.

## Архитектура коротко

- Вход: `bootstrap/app.php`, `routes/web.php`, `routes/api/api.php`.
- Страницы Inertia: `resources/js/pages`. Настройки объекта дополнительно вызывают JSON с этой же сессии через axios (`object/edit.tsx`).
- Доменная логика в `app/Services`, не в моделях. Модели тонкие, кроме каста расписания и удаления журнала напоминаний при смене `notification_date`.
- Страницы дашборда, карточки и настроек объекта собирает `ObjectPageService`. Финансовые записи и их группировку — `ObjectFinanceEntryService`. Запись файла панели и фото показания — `ObjectPropertyFileService`. Контроллеры этих экранов возвращают Inertia или редирект.
- Показания пишет один сервис: `app/Services/Meter/PropertyReadingService.php`. Им пользуются и панель, и `MeterController`.
- Очередь, события и планировщик домена фактически не работают: событие обновления объекта никто не диспатчит, `Schedule` нет.

Подробности: `ARCHITECTURE.md`.

## Куда смотреть перед правкой

| Задача | Сначала открыть |
| --- | --- |
| Объект, карточка, дашборд | `ObjectController`, `ObjectPageService`, `app/Services/Object/Data`, `resources/js/pages/dashboard.tsx`, `object/show.tsx`, `object/edit.tsx` |
| Подписчик и ключ бота | `SubscribeUserController`, `SubscribeUserService`, `UpdateSubscribeUserRequest`, `TelegramUser`, `object/edit.tsx` |
| Счётчик | `ObjectPropertyController`, `ObjectPropertyService`, `CreateObjectPropertyRequest` |
| Показания | `PropertyReadingController`, `PropertyReadingService`, `property-readings.tsx`, для внешнего входа ещё `MeterController` |
| Файлы и фото | `ObjectPropertyFileController`, `ObjectPropertyFileService`, `MeterController`, модель `ObjectPropertyFile` |
| Финансы и Excel | `ObjectFinanceController`, `ObjectFinanceEntryService`, `app/Services/Export`, `ExportPeriodRequest`, `finances.tsx` |
| Расписание напоминаний | `MonthlyNotificationSchedule`, `resources/js/lib/notification-schedule.ts`, `ReadingReminderLog`, `ObjectModel::booted` |
| Доступ | middleware в `routes/web.php` и `bootstrap/app.php`, закомментированные `authorize`, `ObjectModelPolicy` |
| Схема | `database/migrations`, `DATABASE.md` |
| Сброс данных | `ResetApplicationCommand` |

## Бизнес-правила, которые уже есть в коде

- Новый объект называется «Новый объект» и принадлежит текущему пользователю.
- Подписчик создаётся вместе со слотом `telegram_users`: ключ из 8 символов верхнего регистра, `state = main_menu`, `is_admin = false`, `telegram_id = null`. Имя хранится без `@`.
- Пока `telegram_id` не пустой, ключ менять нельзя. Иначе можно передать свой ключ или `regenerate_key`.
- «Зарегистрирован» значит только `telegram_id !== null`.
- Показание за месяц считается сданным, если в текущем календарном месяце есть хотя бы одна строка `property_readings`.
- После любой записи, правки или удаления показания `object_properties.value` становится значением записи с максимальным `recorded_at` (при равенстве — большим `id`) или `null`.
- У показания не больше одного фото: новая загрузка удаляет прежние файлы этого `property_reading_id`.
- Финансовая запись — `income` или `expense`, сумма больше нуля, дата должна существовать в календаре.
- Смена `notification_date` удаляет все `reading_reminder_logs` объекта.
- `order` при создании пишется как `1`.

## Соглашения

- PHP-неймспейсы домена: `App\Models\Object`, `App\Http\Controllers\v1\...`, `App\Services\...`.
- DTO наследуют Spatie `Data` через `BaseDTO`.
- Form request наследуют `BaseRequest` и не ограничивают доступ.
- JSON для axios — ресурсы Laravel с обёрткой `data`. Inertia-страницы получают обычные массивы из контроллеров.
- Имена маршрутов заданы в `routes/web.php` и используются и Ziggy (`route(...)`), и строками axios.
- Комментарии и интерфейс на русском. Не переименовывать пользовательские строки без запроса.
- Стиль PHP в CI — Pint. Стиль фронтенда — Prettier и ESLint. Скрипт `composer cs-fix` не обеспечен зависимостью.

## Проверки

```bash
php artisan test
npm run types
```

`composer test` дополнительно очищает кэш конфигурации. Доменных тестов нет: зелёный прогон не доказывает сохранность объектов, бота и экспорта. CI ещё выполняет `npm run build`, Pint, `npm run format`, `npm run lint` на PHP 8.4 и Node 22.

Не запускать `migrate:fresh` и `php artisan app:reset` на базе, которой пользуется человек или бот.

## Как сломать совместимость с Go-ботом

Этого делать нельзя без явной задачи и сверки с живым ботом:

- переименовать или удалить таблицы и колонки `telegram_users`, `subscribe_users`, `objects`, `object_properties`, `property_readings`, `object_property_files`, `reading_reminder_logs`, а также `user_code` и `bot_properties`;
- сделать `telegram_id` снова `NOT NULL` или менять смысл «зарегистрирован = telegram_id не null»;
- менять уникальность `telegram_id`, формат `registration_key`, значение `state`, которое бот ожидает увидеть у нового слота;
- менять контракт `POST /api/meters/` и `POST /api/meters/{readingId}/photo`: пути, имена `id`, `value`, `file`, форму ответа `status` / `reading_id` / `file_id`;
- включить `GO_API_OBJECT_POST_URL` при общей SQLite или начать диспатчить `AfterUpdateObjectEvent` без проверки, что бот ещё ждёт этот POST;
- менять форму JSON `ObjectEntity`, если выяснится, что какой-то контур его всё ещё читает;
- чистить или пересоздавать общую базу командой `app:reset`.

Код бота здесь недоступен. Молчание PHP-репозитория не означает, что колонку можно удалить.

## Что нельзя предполагать

- Что бот вызывает `/api/meters` и пишет `reading_reminder_logs`, `bot_properties`, `user_code`. Это согласуется со схемой и комментариями, исполнитель не виден.
- Что `verified` требует подтверждённый email.
- Что политика объекта кого-то защищает.
- Что дашборд и карточка одинаково фильтруют владельца. Дашборд фильтрует, карточка по id — нет.
- Что исходящая синхронизация с Go выполняется при сохранении объекта.
- Что `deleted_at` означает мягкое удаление.
- Что `order` отражает положение в интерфейсе.
- Что сидер `DatabaseSeeder` безопасен на пустой базе.
- Что документация новее кода. При расхождении верен код; расхождение нужно поправить в `docs/ai/` в той же задаче.

## Обязательный порядок работы

1. Перед изменением кода прочитать `docs/ai/AI_CONTEXT.md` и документы, относящиеся к задаче: обзор, архитектура, база, API, разработка, текущее состояние.
2. Проверить реальную реализацию в коде, а не полагаться только на документацию.
3. Перед изменением явно определить, какие модули, API, таблицы и интеграции затрагиваются.
4. Не менять публичные контракты и взаимодействие с Go-ботом без явной необходимости.
5. Не делать масштабный рефакторинг внутри локальной задачи без согласования.
6. После изменений обновлять документацию, если изменилось описанное поведение.
7. Запускать доступные релевантные тесты и проверки.
8. В итоге отдельно перечислять, что проверено, что не проверено и какие риски остались.
