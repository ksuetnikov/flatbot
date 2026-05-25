<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Object;

use App\Http\Controllers\Controller;
use App\Http\Requests\Object\StoreObjectPropertyFileRequest;
use App\Http\Requests\Object\UpdateObjectPropertyFileRequest;
use App\Models\Object\ObjectModel;
use App\Models\Object\ObjectProperty;
use App\Models\Object\ObjectPropertyFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ObjectPropertyFileController extends Controller
{
    public function store(
        StoreObjectPropertyFileRequest $request,
        ObjectModel $object,
        int $propertyId,
    ): RedirectResponse {
        $this->assertPropertyBelongsToObject($object, $propertyId);

        $uploaded = $request->file('file');
        $path = $uploaded->store("property-files/{$propertyId}", 'public');
        if ($path === false || ! Storage::disk('public')->exists($path)) {
            return redirect()->back()->withErrors([
                'file' => 'Не удалось сохранить файл в storage. Проверьте права на каталог storage/app/public.',
            ]);
        }

        ObjectPropertyFile::query()->create([
            'object_property_id' => $propertyId,
            'disk' => 'public',
            'path' => $path,
            'original_name' => $uploaded->getClientOriginalName(),
            'mime_type' => $uploaded->getClientMimeType(),
            'size' => $uploaded->getSize(),
        ]);

        return redirect()->back()->with('success', 'Файл прикреплён');
    }

    /** Скачать / открыть вложение (только для авторизованных). */
    public function show(
        ObjectModel $object,
        int $propertyId,
        int $fileId,
    ): StreamedResponse {
        $this->assertPropertyBelongsToObject($object, $propertyId);

        $file = ObjectPropertyFile::query()
            ->where('object_property_id', $propertyId)
            ->findOrFail($fileId);

        if (! $file->existsOnDisk()) {
            abort(404, 'Файл не найден на диске');
        }

        return Storage::disk($file->disk)->response(
            $file->path,
            $file->original_name,
            ['Content-Type' => $file->mime_type ?? 'application/octet-stream'],
        );
    }

    public function update(
        UpdateObjectPropertyFileRequest $request,
        ObjectModel $object,
        int $propertyId,
        int $fileId,
    ): RedirectResponse {
        $this->assertPropertyBelongsToObject($object, $propertyId);

        $file = ObjectPropertyFile::query()
            ->where('object_property_id', $propertyId)
            ->findOrFail($fileId);

        $file->update([
            'original_name' => trim($request->validated('original_name')),
        ]);

        return redirect()->back();
    }

    public function destroy(
        ObjectModel $object,
        int $propertyId,
        int $fileId,
    ): RedirectResponse {
        $this->assertPropertyBelongsToObject($object, $propertyId);

        $file = ObjectPropertyFile::query()
            ->where('object_property_id', $propertyId)
            ->findOrFail($fileId);

        $file->delete();

        return redirect()->back();
    }

    private function assertPropertyBelongsToObject(ObjectModel $object, int $propertyId): void
    {
        ObjectProperty::query()
            ->where('object_id', $object->getKey())
            ->whereKey($propertyId)
            ->firstOrFail();
    }
}
