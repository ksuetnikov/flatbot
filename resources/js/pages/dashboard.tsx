import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Trash2, Pencil } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

interface DashboardProps {
    objects: Array<{
        id: number;
        name: string;
        description?: string;
        created_at: string;
        updated_at: string;
    }>;
}

export default function Dashboard({ objects }: DashboardProps) {
    const handleDelete = (id: number) => {
        if (confirm('Вы уверены, что хотите удалить этот объект?')) {
            router.delete(route('object.destroy', id));
        }
    };

    const showInfo = (id: number) => {
        router.get(route('object.editWithInformation', id));
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {objects.length === 0 ? (
                    <div className="relative min-h-[100vh] flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                        <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                    </div>
                ) : (
                    <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        {objects.map((object) => (
                            <div
                                key={object.id}
                                className="flex flex-col rounded-lg border p-4 shadow-sm dark:border-sidebar-border"
                            >
                                <div className="flex-grow">
                                    <h3 className="font-medium">{object.name}</h3>
                                    {object.description && (
                                        <p className="mt-2 text-sm text-gray-500">
                                            {object.description}
                                        </p>
                                    )}
                                    <div className="mt-4 text-xs text-gray-400">
                                        Добавлено: {new Date(object.created_at).toLocaleDateString()}
                                    </div>
                                </div>

                                <div className="mt-4 flex justify-end space-x-2">
                                    <Button
                                        onClick={() => showInfo(object.id)}
                                        className="inline-flex items-center bg-blue-500 hover:bg-blue-600 text-white"
                                    >
                                        <Pencil className="mr-1.5 h-4 w-4" />
                                        Редактировать
                                    </Button>
                                    <Button
                                        variant="destructive"
                                        onClick={() => handleDelete(object.id)}
                                        className="inline-flex items-center"
                                    >
                                        <Trash2 className="mr-1.5 h-4 w-4" />
                                        Удалить
                                    </Button>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
