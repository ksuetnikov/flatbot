import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';

interface ShowObjectProps {
    object: {
        id: number;
        name: string;
        description?: string;
        created_at: string;
        updated_at: string;
    };
    canEdit: boolean;
}

// Просмотр объекта
export default function ShowObject({ object, canEdit }: ShowObjectProps) {
    return (
        <AppLayout>
            <Head title={`Объект: ${object.name}`} />

            <div className="py-6">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <h1 className="text-2xl font-bold text-gray-900">{object.name}</h1>

                    {object.description && (
                        <p className="mt-4 text-gray-600">{object.description}</p>
                    )}

                    <div className="mt-6 flex space-x-4">
                        {canEdit && (
                            <a
                                href={route('objects.edit', object.id)}
                                className="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                            >
                                Редактировать
                            </a>
                        )}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
