import { ReadingsLineChart, type ChartReadingPoint } from '@/components/object/readings-line-chart';
import AppLayout from '@/layouts/app-layout';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/react';
import {
    ArrowLeft,
    ExternalLink,
    History,
    LineChart,
    Paperclip,
    Pencil,
    Trash2,
} from 'lucide-react';
import { useMemo, useState } from 'react';

interface Reading {
    id: number;
    value: string;
    recorded_at: string;
}

interface PropertyFile {
    id: number;
    original_name: string;
    size: number;
    mime_type: string | null;
    url: string;
}

interface PropertyReadingsProps {
    object: { id: number; name: string };
    property: {
        id: number;
        name: string;
        code: string | null;
        description: string | null;
        readings: Reading[];
        /** С сервера всегда приходит после миграции; optional — чтобы не падать на старом кэше */
        files?: PropertyFile[];
    };
}

import { toDatetimeLocalValue } from '@/lib/notification-schedule';

function formatDateTime(iso: string): string {
    return new Date(iso).toLocaleString('ru-RU', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function formatDate(iso: string): string {
    return new Date(iso).toLocaleDateString('ru-RU', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    });
}

function formatValue(value: string): string {
    const num = Number(value);
    return Number.isNaN(num) ? value : num.toLocaleString('ru-RU');
}

function formatFileSize(bytes: number): string {
    if (bytes < 1024) {
        return `${bytes} Б`;
    }
    if (bytes < 1024 * 1024) {
        return `${(bytes / 1024).toFixed(1)} КБ`;
    }
    return `${(bytes / (1024 * 1024)).toFixed(1)} МБ`;
}

function isImageMime(mime: string | null): boolean {
    if (!mime) {
        return false;
    }
    return mime.startsWith('image/');
}

export default function PropertyReadings({ object, property }: PropertyReadingsProps) {
    const files = property.files ?? [];

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Объекты', href: '/dashboard' },
        { title: object.name, href: route('object.show', object.id) },
        { title: property.name, href: route('property.show', [object.id, property.id]) },
    ];

    const latest = property.readings[0] ?? null;
    const [chartOpen, setChartOpen] = useState(false);
    const [editOpen, setEditOpen] = useState(false);
    const [renameFileOpen, setRenameFileOpen] = useState(false);
    const [renamingFileId, setRenamingFileId] = useState<number | null>(null);
    const [fileInputKey, setFileInputKey] = useState(0);

    const defaultRecordedAt = useMemo(
        () => toDatetimeLocalValue(new Date().toISOString()),
        [],
    );

    const createForm = useForm({
        value: '',
        recorded_at: defaultRecordedAt,
    });

    const editForm = useForm({
        value: '',
        recorded_at: '',
    });

    const [editingId, setEditingId] = useState<number | null>(null);

    const fileForm = useForm<{ file: File | null }>({ file: null });

    const renameFileForm = useForm({ original_name: '' });

    const chartData: ChartReadingPoint[] = useMemo(
        () =>
            [...property.readings]
                .sort(
                    (a, b) =>
                        new Date(a.recorded_at).getTime() - new Date(b.recorded_at).getTime(),
                )
                .map((reading) => ({
                    label: new Date(reading.recorded_at).toLocaleDateString('ru-RU', {
                        day: '2-digit',
                        month: 'short',
                    }),
                    fullLabel: formatDateTime(reading.recorded_at),
                    value: Number(reading.value),
                })),
        [property.readings],
    );

    const submitCreate = (e: React.FormEvent) => {
        e.preventDefault();
        createForm.post(route('property.readings.store', [object.id, property.id]), {
            preserveScroll: true,
            onSuccess: () => {
                createForm.reset();
                createForm.setData('recorded_at', toDatetimeLocalValue(new Date().toISOString()));
            },
        });
    };

    const openEdit = (reading: Reading) => {
        setEditingId(reading.id);
        editForm.setData({
            value: String(reading.value),
            recorded_at: toDatetimeLocalValue(reading.recorded_at),
        });
        setEditOpen(true);
    };

    const submitEdit = (e: React.FormEvent) => {
        e.preventDefault();
        if (editingId == null) return;
        editForm.put(
            route('property.readings.update', [object.id, property.id, editingId]),
            {
                preserveScroll: true,
                onSuccess: () => {
                    setEditOpen(false);
                    setEditingId(null);
                },
            },
        );
    };

    const submitFile = (e: React.FormEvent) => {
        e.preventDefault();
        if (!fileForm.data.file) {
            return;
        }
        fileForm.post(route('property.files.store', [object.id, property.id]), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                fileForm.reset();
                fileForm.setData('file', null);
                setFileInputKey((k) => k + 1);
            },
        });
    };

    const openRenameFile = (file: PropertyFile) => {
        setRenamingFileId(file.id);
        renameFileForm.setData('original_name', file.original_name);
        renameFileForm.clearErrors();
        setRenameFileOpen(true);
    };

    const submitRenameFile = (e: React.FormEvent) => {
        e.preventDefault();
        if (renamingFileId == null) {
            return;
        }
        renameFileForm.put(
            route('property.files.update', [object.id, property.id, renamingFileId]),
            {
                preserveScroll: true,
                onSuccess: () => {
                    setRenameFileOpen(false);
                    setRenamingFileId(null);
                },
            },
        );
    };

    const confirmDeleteFile = (file: PropertyFile) => {
        if (!confirm(`Удалить файл «${file.original_name}»?`)) {
            return;
        }
        router.delete(route('property.files.destroy', [object.id, property.id, file.id]), {
            preserveScroll: true,
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`${property.name} — показания`} />

            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <Button asChild variant="ghost" size="sm" className="mb-2 -ml-2">
                            <Link href={route('object.show', object.id)}>
                                <ArrowLeft className="mr-1 h-4 w-4" />
                                {object.name}
                            </Link>
                        </Button>
                        <h1 className="flex flex-wrap items-center gap-3 text-2xl font-bold tracking-tight">
                            {property.name}
                            {property.code && (
                                <Badge variant="outline" className="font-mono font-normal">
                                    {property.code}
                                </Badge>
                            )}
                        </h1>
                        {property.description && (
                            <p className="mt-1 text-muted-foreground">{property.description}</p>
                        )}
                    </div>
                    <Dialog open={chartOpen} onOpenChange={setChartOpen}>
                        <DialogTrigger asChild>
                            <Button
                                type="button"
                                variant="outline"
                                disabled={property.readings.length === 0}
                            >
                                <LineChart className="mr-2 h-4 w-4" />
                                График показаний
                            </Button>
                        </DialogTrigger>
                        <DialogContent className="max-w-3xl sm:max-w-4xl">
                            <DialogHeader>
                                <DialogTitle>График: {property.name}</DialogTitle>
                                <DialogDescription>
                                    Даты по горизонтали, значения показаний по вертикали
                                </DialogDescription>
                            </DialogHeader>
                            <ReadingsLineChart data={chartData} />
                        </DialogContent>
                    </Dialog>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2 text-lg">
                            <Paperclip className="h-5 w-5" />
                            Вложения
                        </CardTitle>
                        <CardDescription>
                            Фотографии и PDF до 10 МБ каждый. Файлы хранятся локально (диск public); позже можно
                            переключить на облако.
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <form onSubmit={submitFile} className="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-end">
                            <div className="grid flex-1 gap-2 sm:min-w-[200px]">
                                <Label htmlFor="property-file">Файл</Label>
                                <Input
                                    key={fileInputKey}
                                    id="property-file"
                                    type="file"
                                    accept="image/*,.pdf,application/pdf"
                                    onChange={(e) =>
                                        fileForm.setData('file', e.target.files?.[0] ?? null)
                                    }
                                    aria-invalid={!!fileForm.errors.file}
                                />
                                {fileForm.errors.file && (
                                    <p className="text-sm text-destructive">{fileForm.errors.file}</p>
                                )}
                            </div>
                            <Button
                                type="submit"
                                disabled={fileForm.processing || !fileForm.data.file}
                                className="sm:shrink-0"
                            >
                                {fileForm.processing ? 'Загрузка…' : 'Прикрепить'}
                            </Button>
                        </form>

                        {files.length === 0 ? (
                            <p className="text-sm text-muted-foreground">Пока нет вложений</p>
                        ) : (
                            <ul className="divide-y rounded-md border">
                                {files.map((file) => (
                                    <li
                                        key={file.id}
                                        className="flex flex-col gap-3 p-3 sm:flex-row sm:items-center sm:justify-between"
                                    >
                                        <div className="flex min-w-0 flex-1 items-start gap-3">
                                            {isImageMime(file.mime_type) ? (
                                                <a
                                                    href={file.url}
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    className="shrink-0 overflow-hidden rounded border bg-muted"
                                                >
                                                    <img
                                                        src={file.url}
                                                        alt=""
                                                        className="h-16 w-16 object-cover"
                                                    />
                                                </a>
                                            ) : (
                                                <div className="flex h-16 w-16 shrink-0 items-center justify-center rounded border bg-muted text-xs text-muted-foreground">
                                                    PDF
                                                </div>
                                            )}
                                            <div className="min-w-0">
                                                <a
                                                    href={file.url}
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    className="font-medium hover:underline"
                                                >
                                                    {file.original_name}
                                                </a>
                                                <p className="text-sm text-muted-foreground">
                                                    {formatFileSize(file.size)}
                                                    {file.mime_type ? ` · ${file.mime_type}` : ''}
                                                </p>
                                            </div>
                                        </div>
                                        <div className="flex shrink-0 flex-wrap gap-2 sm:justify-end">
                                            <Button variant="outline" size="sm" asChild>
                                                <a href={file.url} target="_blank" rel="noopener noreferrer">
                                                    <ExternalLink className="mr-1 h-4 w-4" />
                                                    Открыть
                                                </a>
                                            </Button>
                                            <Button
                                                type="button"
                                                variant="ghost"
                                                size="sm"
                                                onClick={() => openRenameFile(file)}
                                            >
                                                <Pencil className="mr-1 h-4 w-4" />
                                                Переименовать
                                            </Button>
                                            <Button
                                                type="button"
                                                variant="ghost"
                                                size="sm"
                                                className="text-destructive hover:text-destructive"
                                                onClick={() => confirmDeleteFile(file)}
                                            >
                                                <Trash2 className="mr-1 h-4 w-4" />
                                                Удалить
                                            </Button>
                                        </div>
                                    </li>
                                ))}
                            </ul>
                        )}
                    </CardContent>
                </Card>

                {latest && (
                    <Card className="border-primary/20 bg-primary/5">
                        <CardHeader>
                            <CardDescription>Последнее показание</CardDescription>
                            <CardTitle className="text-4xl tabular-nums">{formatValue(latest.value)}</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p className="text-sm text-muted-foreground">{formatDateTime(latest.recorded_at)}</p>
                        </CardContent>
                    </Card>
                )}

                <Card>
                    <CardHeader>
                        <CardTitle>Добавить показание</CardTitle>
                        <CardDescription>Введите значение и дату/время (календарик в поле даты)</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={submitCreate} className="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-end">
                            <div className="grid flex-1 gap-2 sm:min-w-[160px]">
                                <Label htmlFor="create-value">Показание</Label>
                                <Input
                                    id="create-value"
                                    type="number"
                                    inputMode="decimal"
                                    step="any"
                                    value={createForm.data.value}
                                    onChange={(e) => createForm.setData('value', e.target.value)}
                                    required
                                    aria-invalid={!!createForm.errors.value}
                                />
                                {createForm.errors.value && (
                                    <p className="text-sm text-destructive">{createForm.errors.value}</p>
                                )}
                            </div>
                            <div className="grid flex-1 gap-2 sm:min-w-[220px]">
                                <Label htmlFor="create-recorded">Дата и время</Label>
                                <Input
                                    id="create-recorded"
                                    type="datetime-local"
                                    value={createForm.data.recorded_at}
                                    onChange={(e) => createForm.setData('recorded_at', e.target.value)}
                                    required
                                    aria-invalid={!!createForm.errors.recorded_at}
                                />
                                {createForm.errors.recorded_at && (
                                    <p className="text-sm text-destructive">{createForm.errors.recorded_at}</p>
                                )}
                            </div>
                            <Button type="submit" disabled={createForm.processing} className="sm:shrink-0">
                                {createForm.processing ? 'Сохранение…' : 'Добавить'}
                            </Button>
                        </form>
                    </CardContent>
                </Card>

                <Dialog
                    open={renameFileOpen}
                    onOpenChange={(open) => {
                        setRenameFileOpen(open);
                        if (!open) {
                            setRenamingFileId(null);
                        }
                    }}
                >
                    <DialogContent className="sm:max-w-md">
                        <DialogHeader>
                            <DialogTitle>Переименовать файл</DialogTitle>
                            <DialogDescription>
                                Отображаемое имя в списке вложений. Файл на диске не перемещается.
                            </DialogDescription>
                        </DialogHeader>
                        <form onSubmit={submitRenameFile} className="space-y-4">
                            <div className="grid gap-2">
                                <Label htmlFor="rename-file-name">Имя файла</Label>
                                <Input
                                    id="rename-file-name"
                                    type="text"
                                    value={renameFileForm.data.original_name}
                                    onChange={(e) =>
                                        renameFileForm.setData('original_name', e.target.value)
                                    }
                                    required
                                    maxLength={255}
                                    aria-invalid={!!renameFileForm.errors.original_name}
                                />
                                {renameFileForm.errors.original_name && (
                                    <p className="text-sm text-destructive">
                                        {renameFileForm.errors.original_name}
                                    </p>
                                )}
                            </div>
                            <div className="flex justify-end gap-2">
                                <Button
                                    type="button"
                                    variant="outline"
                                    onClick={() => setRenameFileOpen(false)}
                                >
                                    Отмена
                                </Button>
                                <Button type="submit" disabled={renameFileForm.processing}>
                                    {renameFileForm.processing ? 'Сохранение…' : 'Сохранить'}
                                </Button>
                            </div>
                        </form>
                    </DialogContent>
                </Dialog>

                <Dialog
                    open={editOpen}
                    onOpenChange={(open) => {
                        setEditOpen(open);
                        if (!open) setEditingId(null);
                    }}
                >
                    <DialogContent className="sm:max-w-md">
                        <DialogHeader>
                            <DialogTitle>Изменить показание</DialogTitle>
                            <DialogDescription>Значение и дата/время для выбранной записи</DialogDescription>
                        </DialogHeader>
                        <form onSubmit={submitEdit} className="space-y-4">
                            <div className="grid gap-2">
                                <Label htmlFor="edit-value">Показание</Label>
                                <Input
                                    id="edit-value"
                                    type="number"
                                    inputMode="decimal"
                                    step="any"
                                    value={editForm.data.value}
                                    onChange={(e) => editForm.setData('value', e.target.value)}
                                    required
                                    aria-invalid={!!editForm.errors.value}
                                />
                                {editForm.errors.value && (
                                    <p className="text-sm text-destructive">{editForm.errors.value}</p>
                                )}
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="edit-recorded">Дата и время</Label>
                                <Input
                                    id="edit-recorded"
                                    type="datetime-local"
                                    value={editForm.data.recorded_at}
                                    onChange={(e) => editForm.setData('recorded_at', e.target.value)}
                                    required
                                    aria-invalid={!!editForm.errors.recorded_at}
                                />
                                {editForm.errors.recorded_at && (
                                    <p className="text-sm text-destructive">{editForm.errors.recorded_at}</p>
                                )}
                            </div>
                            <div className="flex justify-end gap-2">
                                <Button type="button" variant="outline" onClick={() => setEditOpen(false)}>
                                    Отмена
                                </Button>
                                <Button type="submit" disabled={editForm.processing}>
                                    {editForm.processing ? 'Сохранение…' : 'Сохранить'}
                                </Button>
                            </div>
                        </form>
                    </DialogContent>
                </Dialog>

                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2 text-lg">
                            <History className="h-5 w-5" />
                            История показаний
                        </CardTitle>
                        <CardDescription>
                            {property.readings.length > 0
                                ? `Всего записей: ${property.readings.length}`
                                : 'Добавьте показание выше или передайте через Telegram-бота'}
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="p-0">
                        {property.readings.length === 0 ? (
                            <p className="px-6 pb-6 text-sm text-muted-foreground">История пуста</p>
                        ) : (
                            <div className="overflow-x-auto">
                                <table className="w-full text-sm">
                                    <thead>
                                        <tr className="border-b bg-muted/50 text-left text-muted-foreground">
                                            <th className="px-6 py-3 font-medium">Дата</th>
                                            <th className="px-6 py-3 font-medium">Время</th>
                                            <th className="px-6 py-3 text-right font-medium">Показание</th>
                                            <th className="w-[100px] px-6 py-3 text-right font-medium">
                                                Действия
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {property.readings.map((reading, index) => {
                                            const date = new Date(reading.recorded_at);
                                            return (
                                                <tr
                                                    key={reading.id}
                                                    className={
                                                        index === 0
                                                            ? 'border-b bg-primary/5'
                                                            : 'border-b last:border-0 hover:bg-muted/30'
                                                    }
                                                >
                                                    <td className="px-6 py-3">{formatDate(reading.recorded_at)}</td>
                                                    <td className="px-6 py-3 text-muted-foreground">
                                                        {date.toLocaleTimeString('ru-RU', {
                                                            hour: '2-digit',
                                                            minute: '2-digit',
                                                        })}
                                                    </td>
                                                    <td className="px-6 py-3 text-right font-semibold tabular-nums">
                                                        {formatValue(reading.value)}
                                                    </td>
                                                    <td className="px-6 py-3 text-right">
                                                        <Button
                                                            type="button"
                                                            variant="ghost"
                                                            size="sm"
                                                            onClick={() => openEdit(reading)}
                                                        >
                                                            <Pencil className="mr-1 h-4 w-4" />
                                                            Изменить
                                                        </Button>
                                                    </td>
                                                </tr>
                                            );
                                        })}
                                    </tbody>
                                </table>
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
