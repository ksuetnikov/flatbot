import AppLayout from '@/layouts/app-layout';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { Gauge, Settings2, Users } from 'lucide-react';

interface LatestReading {
    value: string;
    recorded_at: string;
}

interface PropertySummary {
    id: number;
    name: string;
    code: string | null;
    description: string | null;
    latest_reading: LatestReading | null;
}

interface ObjectShowProps {
    object: {
        id: number;
        name: string;
        description?: string | null;
        notification_date?: string | null;
        created_at: string;
        subscribers: Array<{ id: number; name: string }>;
        properties: PropertySummary[];
    };
}

function formatDateTime(iso: string): string {
    return new Date(iso).toLocaleString('ru-RU', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function formatValue(value: string): string {
    const num = Number(value);
    return Number.isNaN(num) ? value : num.toLocaleString('ru-RU');
}

export default function ObjectShow({ object }: ObjectShowProps) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Объекты', href: '/dashboard' },
        { title: object.name, href: route('object.show', object.id) },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={object.name} />

            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight md:text-3xl">{object.name}</h1>
                        {object.description && (
                            <p className="mt-2 max-w-2xl text-muted-foreground">{object.description}</p>
                        )}
                    </div>
                    <Button asChild variant="outline" className="shrink-0">
                        <Link href={route('object.edit', object.id)}>
                            <Settings2 className="mr-2 h-4 w-4" />
                            Настройки
                        </Link>
                    </Button>
                </div>

                <div className="grid gap-4 sm:grid-cols-2">
                    <Card>
                        <CardHeader className="pb-2">
                            <CardDescription className="flex items-center gap-2">
                                <Users className="h-4 w-4" />
                                Подписчики
                            </CardDescription>
                            <CardTitle className="text-3xl">{object.subscribers.length}</CardTitle>
                        </CardHeader>
                    </Card>
                    <Card>
                        <CardHeader className="pb-2">
                            <CardDescription className="flex items-center gap-2">
                                <Gauge className="h-4 w-4" />
                                Счётчики
                            </CardDescription>
                            <CardTitle className="text-3xl">{object.properties.length}</CardTitle>
                        </CardHeader>
                    </Card>
                </div>

                <div>
                    <h2 className="mb-4 text-lg font-semibold">Показания счётчиков</h2>

                    {object.properties.length === 0 ? (
                        <Card className="border-dashed">
                            <CardContent className="py-10 text-center text-muted-foreground">
                                Счётчики не настроены. Добавьте их в настройках объекта.
                            </CardContent>
                        </Card>
                    ) : (
                        <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                            {object.properties.map((property) => (
                                <Link
                                    key={property.id}
                                    href={route('property.show', [object.id, property.id])}
                                    className="group block transition-transform hover:scale-[1.01]"
                                >
                                    <Card className="h-full border-border/80 transition-shadow group-hover:border-primary/40 group-hover:shadow-md">
                                        <CardHeader>
                                            <div className="flex items-start justify-between gap-2">
                                                <CardTitle className="text-base">{property.name}</CardTitle>
                                                {property.code && (
                                                    <Badge variant="secondary" className="shrink-0 font-mono text-xs">
                                                        {property.code}
                                                    </Badge>
                                                )}
                                            </div>
                                            {property.description && (
                                                <CardDescription>{property.description}</CardDescription>
                                            )}
                                        </CardHeader>
                                        <CardContent>
                                            {property.latest_reading ? (
                                                <div>
                                                    <p className="text-3xl font-bold tabular-nums text-primary">
                                                        {formatValue(property.latest_reading.value)}
                                                    </p>
                                                    <p className="mt-2 text-sm text-muted-foreground">
                                                        {formatDateTime(property.latest_reading.recorded_at)}
                                                    </p>
                                                </div>
                                            ) : (
                                                <p className="text-sm text-muted-foreground">Показаний пока нет</p>
                                            )}
                                            <p className="mt-4 text-xs font-medium text-primary opacity-0 transition-opacity group-hover:opacity-100">
                                                История показаний →
                                            </p>
                                        </CardContent>
                                    </Card>
                                </Link>
                            ))}
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
