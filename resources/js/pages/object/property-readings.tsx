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
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { ArrowLeft, History, LineChart } from 'lucide-react';
import { useMemo, useState } from 'react';

interface Reading {
    id: number;
    value: string;
    recorded_at: string;
}

interface PropertyReadingsProps {
    object: { id: number; name: string };
    property: {
        id: number;
        name: string;
        code: string | null;
        description: string | null;
        readings: Reading[];
    };
}

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

export default function PropertyReadings({ object, property }: PropertyReadingsProps) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Объекты', href: '/dashboard' },
        { title: object.name, href: route('object.show', object.id) },
        { title: property.name, href: route('property.show', [object.id, property.id]) },
    ];

    const latest = property.readings[0] ?? null;
    const [chartOpen, setChartOpen] = useState(false);

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
                        <CardTitle className="flex items-center gap-2 text-lg">
                            <History className="h-5 w-5" />
                            История показаний
                        </CardTitle>
                        <CardDescription>
                            {property.readings.length > 0
                                ? `Всего записей: ${property.readings.length}`
                                : 'Показания появятся после передачи через Telegram-бота'}
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
