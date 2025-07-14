<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\SubscribeUser;

use App\Http\Controllers\Controller;
use App\Http\DTO\SubscribeUser\SubscribeUserDTO;
use App\Http\Services\SubscribeUser\SubscribeUserService;
use App\Models\Object\ObjectModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

/**
 * @author Kirill Suetnikov
 */
class SubscribeUserController extends Controller
{
    public function __construct(
        private readonly SubscribeUserService $subscribeUserService
    ) {}

    public function create(Request $request, ObjectModel $object): RedirectResponse
    {
        // $this->authorize('update', $object);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        // $a = SubscribeUserDTO::fromRequest($validated);
        \Log::info('validated:');
        \Log::info($validated);
        $dto = SubscribeUserDTO::from($validated);
        \Log::info($dto);
        $this
            ->subscribeUserService
            ->create(
            objectId: $object->getKey(),
            dto: $dto
        );
        // $object->update($validated);

        return redirect()->route('dashboard')
            ->with('success', 'Объект успешно обновлен');
    }

    /** Удаление объекта
     *
     * @param ObjectModel $object
     *
     * @return RedirectResponse
     */
    public function destroy(ObjectModel $object): RedirectResponse
    {
        // $this->authorize('delete', $object);

        $object->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Объект успешно удален');
    }
}
