<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Object;

use App\Http\Controllers\Controller;
use App\Http\Requests\Object\UpdateObjectRequest;
use App\Models\Object\ObjectModel;
use App\Services\Object\ObjectService;
use Auth;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * @author Kirill Suetnikov
 */
class ObjectController extends Controller
{
    public function __construct(
        private readonly ObjectService $objectService
    ) {
    }

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
            ]
        );
    }

    /** Создание объекта
     *
     * @return RedirectResponse
     */
    public function create(): RedirectResponse
    {
        $user = Auth::getUser();
        $this->objectService->create($user);

        return redirect()->route('dashboard')
            ->with('success', 'Объект успешно создан');
    }

    /** Само редактирование
     *
     * @param UpdateObjectRequest $request
     * @param ObjectModel $object
     *
     * @return RedirectResponse
     */

    public function update(UpdateObjectRequest $request, ObjectModel $object): RedirectResponse
    {
        // $this->authorize('update', $object);

        $object->update($request->validated());

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
