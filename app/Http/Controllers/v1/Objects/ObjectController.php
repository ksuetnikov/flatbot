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
        $object->load('subscribers');
        return Inertia::render(
            component: 'object/edit',
            props:[
            'object' => $object,
            'allSubscribers' => $object->subscribers,
        ]);
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
