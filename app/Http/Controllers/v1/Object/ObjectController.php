<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Object;

use App\Http\Controllers\Controller;
use App\Http\Requests\Object\UpdateObjectRequest;
use App\Models\Object\ObjectModel;
use App\Models\User;
use App\Services\Object\ObjectPageService;
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
        private readonly ObjectService $objectService,
        private readonly ObjectPageService $objectPageService,
    ) {}

    /**
     * Список объектов текущего пользователя.
     */
    public function dashboard(): Response
    {
        $user = Auth::user();
        if (! $user instanceof User) {
            abort(401);
        }

        return Inertia::render('dashboard', [
            'objects' => $this->objectPageService->dashboardObjects($user),
        ]);
    }

    /**
     * Обзор объекта: счётчики и последние показания.
     */
    public function show(ObjectModel $object): Response
    {
        return Inertia::render('object/show', [
            'object' => $this->objectPageService->show($object),
        ]);
    }

    /**
     * Настройки объекта: подписчики и счётчики.
     */
    public function edit(ObjectModel $object): Response
    {
        return Inertia::render('object/edit', $this->objectPageService->edit($object));
    }

    /**
     * Создание объекта.
     */
    public function create(): RedirectResponse
    {
        $user = Auth::getUser();
        $object = $this->objectService->create($user);

        return redirect()->route('object.show', $object)
            ->with('success', 'Объект успешно создан');
    }

    /**
     * Сохранение названия, описания и расписания напоминаний.
     */
    public function update(UpdateObjectRequest $request, ObjectModel $object): RedirectResponse
    {
        // $this->authorize('update', $object);

        $object->update($request->validated());

        return redirect()->route('object.show', $object)
            ->with('success', 'Объект успешно обновлен');
    }

    /**
     * Удаление объекта.
     */
    public function destroy(ObjectModel $object): RedirectResponse
    {
        // $this->authorize('delete', $object);

        $object->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Объект успешно удален');
    }
}
