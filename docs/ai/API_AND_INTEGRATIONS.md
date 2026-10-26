# HTTP, доступ и интеграции

Префикс веб-приложения не задан. Префикс JSON-приёма показаний — `/api`. Версионирования URL нет, несмотря на неймспейс контроллеров `v1`.

## Аутентификация

Веб-сессия Laravel (стартовый kit): регистрация, логин, logout, сброс пароля, подтверждение пароля, письма верификации. Guard и брокер — стандартные, отдельных токенов API нет.

Группа объектов в `routes/web.php` закрыта `auth` и `verified`. `User` не реализует `Illuminate\Contracts\Auth\MustVerifyEmail`, поэтому `verified` не блокирует неподтверждённый email.

Настройки (`routes/settings.php`) закрыты только `auth`.

`BaseRequest::authorize()` возвращает `true`. Политика объекта не применяется: вызовы `authorize` закомментированы, класс политики ссылается на неверный namespace и всегда запрещает действие.

Привязка `{object}` не ограничена `user_id`. Проверка «счётчик принадлежит объекту» есть у показаний, файлов и экспорта. Её нет у удаления счётчика и у операций с подписчиком: id ищется глобально.

Группа `/api` в `bootstrap/app.php` подключена без `api`, `auth`, `web` и throttle. CSRF на неё не распространяется.

## Веб-маршруты панели

Имена и ограничения тел — из контроллеров и form request.

| Метод и путь | Имя | Ответ |
| --- | --- | --- |
| `GET /` | `home` | Гость: Inertia `welcome`. Пользователь: редирект на `dashboard` |
| `GET /dashboard` | `dashboard` | Inertia `dashboard`, объекты текущего пользователя и `finance_summary` |
| `POST /objects` | `object.create` | Редирект на карточку |
| `GET /objects/{object}` | `object.show` | Inertia `object/show` |
| `GET /objects/{object}/edit` | `object.edit` | Inertia `object/edit` |
| `PUT /objects/{object}` | `object.update` | Поля: `name` required max 255, `description` nullable max 255, `notification_date` nullable date. Пустая строка даты становится `null` |
| `DELETE /objects/{object}` | `object.destroy` | Редирект на дашборд |
| `POST /objects/{object}/subscribers` | `subscriber.create` | JSON `SubscribeUserGetResource`. Тело: `name` required max 255 |
| `PUT /objects/{object}/subscribers/{subscriberId}` | `subscriber.update` | Тело: `name`; `registration_key` иногда, nullable, 4–32, unique по `telegram_users.registration_key`; `regenerate_key` boolean |
| `DELETE /objects/{object}/subscribers/{subscriberId}` | `subscriber.destroy` | 204 |
| `POST /objects/{object}/properties` | `property.create` | JSON ресурса. `name`, `code` required max 255 |
| `GET /objects/{object}/properties/{propertyId}` | `property.show` | Inertia `object/property-readings` |
| `PUT /objects/{object}/properties/{propertyId}` | `property.update` | JSON. Те же `name`, `code` |
| `DELETE /objects/{object}/properties/{propertyId}` | `property.destroy` | 204 |
| `POST /objects/{object}/properties/{propertyId}/readings` | `property.readings.store` | `value` numeric, `recorded_at` date |
| `PUT .../readings/{readingId}` | `property.readings.update` | те же поля |
| `DELETE .../readings/{readingId}` | `property.readings.destroy` | редирект назад |
| `GET .../readings/export` | `property.readings.export` | XLSX |
| `POST .../files` | `property.files.store` | файл до 10240 КБ, mime jpg, jpeg, png, gif, webp, pdf |
| `GET .../files/{fileId}` | `property.files.show` | поток файла с диска |
| `PUT .../files/{fileId}` | `property.files.update` | `original_name` required max 255, без `/` и `\` |
| `DELETE .../files/{fileId}` | `property.files.destroy` | редирект назад |
| `GET /objects/{object}/finances` | `object.finances` | Inertia `object/finances` |
| `POST /objects/{object}/finances` | `object.finances.store` | `year` 2000–2100, `month` 1–12, `day` 1–31 и `checkdate`, `type` `income`\|`expense`, `amount` numeric min 0.01, `comment` nullable max 500 |
| `DELETE /objects/{object}/finances/{entryId}` | `object.finances.destroy` | запись должна принадлежать объекту |
| `GET /objects/{object}/finances/export` | `object.finances.export` | XLSX |

Экспорт, query-параметры необязательны и должны быть парами: `year_from` + `month_from`, `year_to` + `month_to`. Если заданы оба края, начало не позже конца. Пустые параметры означают весь период.

JSON-ресурсы Laravel оборачиваются в `data`. Поля подписчика: `id`, `name`, `order`, `registrationKey`, `isRegistered`, `telegramUsername`, `createdAt`. Поля счётчика: `id`, `name`, `code`, `value`, `order`, `createdAt`.

Карточка объекта (Inertia, не ресурс) дополнительно содержит `finance_summary` (`total_income`, `total_expense`, `balance`, `entries_count`) и `reading_status` (`period_label`, `is_complete`, `submitted_count`, `total_count`, `missing_properties`, `properties`, `subscribers`).

XLSX показаний: колонки «Дата», «Объект», «Показание», «Счётчик», лист «Показания», имя `pokazaniya-{objectId}-{propertyId}-{Y-m-d}.xlsx`. Дата форматируется в `config('app.timezone')`.

XLSX финансов: «Дата», «Объект», «Тип», «Сумма», «Комментарий», лист «Финансы», имя `finansy-{objectId}-{Y-m-d}.xlsx`. Тип в файле — «Доход» или «Расход».

В обоих файлах снизу добавляются строки итогов периода из `FinancePeriodTotalsService`.

Ошибки валидации веб-форм — стандартный 422 Laravel. Для axios страница настроек читает `errors.name` и `errors.registration_key`.

## API приёма показаний

| Метод и путь | Имя | Тело | Успех |
| --- | --- | --- | --- |
| `POST /api/meters/` | `meter.create` | JSON `id` integer, exists `object_properties.id`; `value` numeric | `200` `{ "status": "ok", "reading_id": <int> }`. `recorded_at` ставится `now()` |
| `POST /api/meters/{readingId}/photo` | `meter.photo` | multipart `file`: jpg, jpeg, png, gif, webp, max 10240 КБ. PDF здесь не принимается | `200` `{ "status": "ok", "file_id": <int> }` |

Несуществующий `readingId` — 404. Чужой или несуществующий `id` счётчика — 422 из-за `exists`. Ошибки валидации — 422. Другого формата ошибки и идемпотентного ключа нет.

Повтор фото на тот же `readingId` удаляет предыдущие файлы этого показания и пишет новый. Повтор показания всегда создаёт новую строку истории, дедупликации нет.

Аутентификации, подписи и списка разрешённых IP в коде нет.

## Исходящий вызов Go

`UpdateObjectJob` отправляет `POST` с телом JSON (`Content-Type: application/json`) на `config('services.go_api.object_post_url')`, переменная окружения `GO_API_OBJECT_POST_URL`. В `.env.example` этой переменной нет.

Тело — `ObjectEntity`: `id`, `name`, `description`, `user_id`, `order`, `notification_date`, `created_at`, `updated_at`, `deleted_at`, массивы `subscribers` и `properties`.

Подписчик в JSON: `id`, `name`, `description`, `object_id`, `order`, `created_at`, `updated_at`, `deleted_at`. Ключа регистрации и `telegram_user_id` в этой сериализации нет.

Счётчик в JSON: `id`, `name`, `code`, `description`, `object_id`, `order`, timestamps и `deleted_at`. Поля `value` в сущности нет.

Неуспешный HTTP и исключения логируются (`Не удалось отправить объект в Go API` / `Ошибка при отправке данных в Go API`) и не пробрасываются. Повторной отправки нет.

Событие, которое ставит задачу, в репозитории не диспатчится. При пустом URL подписчик события выходит сразу. Комментарий конфигурации запрещает задавать URL при общей базе с ботом.

## Общая база с Go-ботом

Это описанный в коде способ совместной работы. Контракт — таблицы из `DATABASE.md`, в первую очередь `telegram_users`, `subscribe_users`, `objects`, `object_properties`, `property_readings`, `object_property_files`, `reading_reminder_logs`.

Сидер `TelegramUserSeeder` в комментарии ссылается на `goBot/migrations/init.sql`. Этого файла в репозитории нет. Сидер делает `updateOrInsert` слота с фиксированным ключом и `is_admin = true`. Значение ключа и username в документацию не переносятся: они зашиты в сидере.

`php artisan app:reset` в конце пишет, что goBot нужно перезапустить, если он был запущен. Как бот подключается к той же SQLite, в этом репозитории не задано.

## Прочие внешние сервисы

`config/services.php` содержит заготовки starter kit: Postmark, SES, Resend, Slack. В коде приложения вызовов к ним нет. Почта в `.env.example` — `MAIL_MAILER=log`.

Вебхуков входящих, кроме двух маршрутов `/api/meters`, нет.

## Ошибки и повторы

- Очередь разработки: `php artisan queue:listen --tries=1`. Задача Go при ошибке сети сама помечает успех, потому что исключение перехвачено.
- Планировщика повторов нет.
- Загрузка файла панели при неудаче записи на диск возвращает ошибку поля `file` и не создаёт строку. API в том же случае бросает `RuntimeException` («Не удалось сохранить файл в storage.»).
- `composer run dev` поднимает `artisan serve` с `upload_max_filesize=12M` и `post_max_size=12M`. Лимит валидации файла — 10 МБ. Сообщение form request прямо говорит, что без этих лимитов PHP файл может не дойти до валидации.
