import AppLayout from '@/layouts/app-layout';
import { Head, useForm } from '@inertiajs/react';
import { Button } from '@/components/ui/button';

interface EditObjectProps {
    object: {
        id: number;
        name: string;
        description?: string;
    };
}

// Редактирование объекта
export default function EditObject({ object }: EditObjectProps) {
    console.log('we are here!');
    const { data, setData, put, processing, errors } = useForm({
        name: object.name,
        description: object.description || '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('objects.edit', object.id));
    };

    return (
        <AppLayout>
            <Head title={`Редактирование: ${object.name}`} />

            <div className="py-6">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <h1 className="text-2xl font-bold text-gray-900">Редактирование объекта</h1>

                    <form onSubmit={handleSubmit} className="mt-6 space-y-6">
                        <div>
                            <label htmlFor="name" className="block text-sm font-medium text-gray-700">
                                Название
                            </label>
                            <input
                                id="name"
                                type="text"
                                value={data.name}
                                onChange={(e) => setData('name', e.target.value)}
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            />
                            {errors.name && <p className="mt-2 text-sm text-red-600">{errors.name}</p>}
                        </div>

                        <div>
                            <label htmlFor="description" className="block text-sm font-medium text-gray-700">
                                Описание
                            </label>
                            <textarea
                                id="description"
                                rows={4}
                                value={data.description}
                                onChange={(e) => setData('description', e.target.value)}
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            />
                            {errors.description && <p className="mt-2 text-sm text-red-600">{errors.description}</p>}
                        </div>

                        <div className="flex space-x-4">
                            <Button type="submit" disabled={processing}>
                                Сохранить
                            </Button>
                            <a
                                href={route('objects.show', object.id)}
                                className="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300"
                            >
                                Отмена
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}
