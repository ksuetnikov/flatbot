import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

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
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
                {objects.length === 0 ? (
                    <div className="relative min-h-[100vh] flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                        <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                    </div>
                ) : (
                    <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        {objects.map((object) => (
                            <div
                                key={object.id}
                                className="p-4 border rounded-lg shadow-sm dark:border-sidebar-border"
                            >
                                <h3 className="font-medium">{object.name}</h3>
                                {object.description && (
                                    <p className="text-sm text-gray-500 mt-2">
                                        {object.description}
                                    </p>
                                )}
                                <div className="mt-4 text-xs text-gray-400">
                                    Created: {new Date(object.created_at).toLocaleDateString()}
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
