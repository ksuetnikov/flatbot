<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Objects;

use App\Http\Controllers\Controller;
use App\Models\Object\ObjectModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

/**
 * @author Kirill Suetnikov
 */
class ObjectController extends Controller
{
    // Просмотр объекта
    public function show(ObjectModel $object)
    {
        // $this->authorize('view', $object); // Проверка прав доступа

        return Inertia::render('object/show', [
            'object' => $object,
            'canEdit' => true // или ваша логика проверки прав
        ]);
    }

    /** Форма редактирования
     *
     * @param ObjectModel $object
     *
     * @return Response
     */
    public function edit(ObjectModel $object): Response
    {
        // $this->authorize('update', $object); // Проверка прав доступа
        \Log::info('object edit');
        \Log::info($object);

        return Inertia::render(
            component: 'object/edit',
            props:[
            'object' => $object
        ]);
    }

    // Обновление объекта
    public function update(Request $request, ObjectModel $object): RedirectResponse
    {
        // $this->authorize('update', $object);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            // другие поля
        ]);

        $object->update($validated);

        return redirect()->route('dashboard')
            ->with('success', 'Объект успешно обновлен');
    }

    // Удаление объекта
    public function destroy(ObjectModel $object)
    {
        // $this->authorize('delete', $object);

        $object->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Объект успешно удален');
    }
}
