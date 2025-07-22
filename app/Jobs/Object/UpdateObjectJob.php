<?php

declare(strict_types=1);

namespace App\Jobs\Object;

use App\DTO\Object\AfterUpdateObjectDTO;
use App\Entities\ObjectEntity;
use App\Models\Object\ObjectModel;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * @author Kirill Suetnikov
 */
class UpdateObjectJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /** @var Carbon */
    public Carbon $date;

    /**
     * @param AfterUpdateObjectDTO $dto
     */
    public function __construct(
        private readonly AfterUpdateObjectDTO $dto,
    ) {
        $this->date = now();
    }

    /**
     * @return void
     *
     * @throws Throwable
     */
    public function handle(): void
    {
        $this->sendInfoToOtherServices();
    }

    /**
     * @return void
     */
    public function sendInfoToOtherServices(): void
    {
        $object = ObjectModel::findOrFail($this->dto->objectId)->load([
            'subscribers',
            'properties',
        ]);
        $entity = ObjectEntity::fromArray($object->toArray());
        $json = json_encode($entity, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        // URL Go-сервиса, желательно вынести в .env
        $url = config('services.go_api.object_post_url'); // например: http://localhost:8080/api/objects

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->withBody($json, 'application/json')
                ->post($url);

            if (!$response->successful()) {
                logger()->error('Не удалось отправить объект в Go API', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            logger()->error('Ошибка при отправке данных в Go API', [
                'message' => $e->getMessage(),
            ]);
        }
    }
}
