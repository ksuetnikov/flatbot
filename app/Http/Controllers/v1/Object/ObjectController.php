<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Object;

use App\DTO\Object\AfterUpdateObjectDTO;
use App\Events\Object\AfterUpdateObjectEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\Object\ObjectCreateResource;
use App\Models\Object\ObjectModel;
use App\Services\Object\ObjectService;
use Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * @author Kirill Suetnikov
 */
class ObjectController extends Controller
{
    public function __construct(
        private readonly ObjectService $objectService
    ) {}

    /** Форма редактирования
     *
     * @param ObjectModel $object
     *
     * @return Response
     */
    public function show(ObjectModel $object): Response
    {
        // $this->authorize('update', $object); // Проверка прав доступа
        \Log::info('object edit');
        \Log::info($object);
        $object->load(['subscribers', 'properties']);
        return Inertia::render(
            component: 'object/edit',
            props:[
            'object' => $object,
            'allSubscribers' => $object->subscribers,
            'allProperties' => $object->properties,
        ]);
    }

    /** Создание объекта
     *
     * @return RedirectResponse
     */
    public function create(): RedirectResponse
    {
        $user = Auth::getUser();
        $object = $this
            ->objectService
            ->create($user);
        $eventDigestStructureDto = AfterUpdateObjectDTO::from([
            'objectId' => $object->getKey(),
        ]);
        AfterUpdateObjectEvent::dispatch($eventDigestStructureDto);
        return redirect()->route('dashboard')
            ->with('success', 'Объект успешно обновлен');
    }

    /** Само редактирование
     *
     * @param Request $request
     * @param ObjectModel $object
     *
     * @return RedirectResponse
     */

    public function update(Request $request, ObjectModel $object): RedirectResponse
    {
        // $this->authorize('update', $object);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            // другие поля
        ]);

        $object->update($validated);
        $eventDigestStructureDto = AfterUpdateObjectDTO::from([
            'objectId' => $object->getKey(),
        ]);
        AfterUpdateObjectEvent::dispatch($eventDigestStructureDto);

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
