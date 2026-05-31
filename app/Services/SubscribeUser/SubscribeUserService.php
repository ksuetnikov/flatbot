<?php

declare(strict_types=1);

namespace App\Services\SubscribeUser;

use App\DTO\SubscribeUser\SubscribeUserDTO;
use App\Models\SubscribeUser\SubscribeUser;
use App\Models\TelegramUser\TelegramUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * @author Kirill Suetnikov
 */
class SubscribeUserService
{
    public function create(int $objectId, SubscribeUserDTO $dto): SubscribeUser
    {
        return DB::transaction(function () use ($objectId, $dto) {
            $telegramUser = $this->createTelegramUser();

            $subscribeUser = new SubscribeUser();
            $subscribeUser->name = $this->normalizeUsername($dto->name);
            $subscribeUser->object_id = $objectId;
            $subscribeUser->telegram_user_id = $telegramUser->id;
            $subscribeUser->order = 1;
            $subscribeUser->save();

            return $subscribeUser->load('telegramUser');
        });
    }

    public function update(SubscribeUser $subscribeUser, SubscribeUserDTO $dto): SubscribeUser
    {
        return DB::transaction(function () use ($subscribeUser, $dto) {
            $subscribeUser->name = $this->normalizeUsername($dto->name);
            $subscribeUser->save();

            $telegramUser = $this->ensureTelegramUser($subscribeUser);

            if ($telegramUser->isRegistered() && ($dto->regenerate_key || $dto->registration_key !== null)) {
                throw ValidationException::withMessages([
                    'registration_key' => ['Нельзя менять ключ у уже зарегистрированного пользователя.'],
                ]);
            }

            if ($dto->regenerate_key) {
                $telegramUser->registration_key = $this->generateRegistrationKey();
                $telegramUser->save();
            } elseif ($dto->registration_key !== null && $dto->registration_key !== '') {
                $telegramUser->registration_key = $dto->registration_key;
                $telegramUser->save();
            }

            return $subscribeUser->load('telegramUser');
        });
    }

    public function delete(int $subscribeUserId): void
    {
        $subscribeUser = SubscribeUser::query()->findOrFail($subscribeUserId);

        DB::transaction(function () use ($subscribeUser) {
            $telegramUserId = $subscribeUser->telegram_user_id;
            $subscribeUser->delete();

            if ($telegramUserId !== null) {
                TelegramUser::destroy($telegramUserId);
            }
        });
    }

    public function backfillTelegramUsers(int $objectId): void
    {
        DB::transaction(function () use ($objectId) {
            SubscribeUser::query()
                ->where('object_id', $objectId)
                ->whereNull('telegram_user_id')
                ->each(function (SubscribeUser $subscribeUser) {
                    $this->ensureTelegramUser($subscribeUser);
                });
        });
    }

    private function ensureTelegramUser(SubscribeUser $subscribeUser): TelegramUser
    {
        if ($subscribeUser->telegram_user_id !== null) {
            return TelegramUser::query()->findOrFail($subscribeUser->telegram_user_id);
        }

        $telegramUser = $this->createTelegramUser();
        $subscribeUser->telegram_user_id = $telegramUser->id;
        $subscribeUser->save();

        return $telegramUser;
    }

    private function createTelegramUser(): TelegramUser
    {
        $telegramUser = new TelegramUser();
        $telegramUser->registration_key = $this->generateRegistrationKey();
        $telegramUser->state = 'main_menu';
        $telegramUser->is_admin = false;
        $telegramUser->created_at = now();
        $telegramUser->save();

        return $telegramUser;
    }

    private function generateRegistrationKey(): string
    {
        do {
            $key = Str::upper(Str::random(8));
        } while (TelegramUser::query()->where('registration_key', $key)->exists());

        return $key;
    }

    private function normalizeUsername(string $name): string
    {
        return ltrim(trim($name), '@');
    }
}
