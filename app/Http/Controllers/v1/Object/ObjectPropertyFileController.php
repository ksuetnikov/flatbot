<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Object;

use App\Http\Controllers\Controller;
use App\Http\Requests\Object\StoreObjectPropertyFileRequest;
use App\Http\Requests\Object\UpdateObjectPropertyFileRequest;
use App\Models\Object\ObjectModel;
use App\Models\Object\ObjectProperty;
use App\Models\Object\ObjectPropertyFile;
use App\Services\Object\ObjectPropertyFileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ObjectPropertyFileController extends Controller
{
    public function __construct(
        private readonly ObjectPropertyFileService $objectPropertyFileService,
    ) {}

    public function store(
        StoreObjectPropertyFileRequest $request,
        ObjectModel $object,
        int $propertyId,
    ): RedirectResponse {
        $property = $this->propertyForObject($object, $propertyId);
        $uploaded = $request->file('file');

        if (! $uploaded instanceof UploadedFile) {
            return $this->storageFailed();
        }

        try {
            $this->objectPropertyFileService->store($property, $uploaded);
        } catch (RuntimeException) {
            return $this->storageFailed();
        }

        return redirect()->back()->with('success', 'Файл прикреплён');
    }

    /**
     * Скачать или открыть вложение.
     */
    public function show(
        ObjectModel $object,
        int $propertyId,
        int $fileId,
    ): StreamedResponse {
        $this->propertyForObject($object, $propertyId);

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
        $this->propertyForObject($object, $propertyId);

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
        $this->propertyForObject($object, $propertyId);

        $file = ObjectPropertyFile::query()
            ->where('object_property_id', $propertyId)
            ->findOrFail($fileId);

        $file->delete();

        return redirect()->back();
    }

    private function propertyForObject(ObjectModel $object, int $propertyId): ObjectProperty
    {
        return ObjectProperty::query()
            ->where('object_id', $object->id)
            ->whereKey($propertyId)
            ->firstOrFail();
    }

    private function storageFailed(): RedirectResponse
    {
        return redirect()->back()->withErrors([
            'file' => 'Не удалось сохранить файл в storage. Проверьте права на каталог storage/app/public.',
        ]);
    }
}
