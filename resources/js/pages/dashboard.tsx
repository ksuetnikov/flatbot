import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/react';
import { Building2, CirclePlus, Trash2, Wallet } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Объекты',
        href: '/dashboard',
    },
];

interface FinanceSummary {
    total_income: number;
    total_expense: number;
    balance: number;
    entries_count: number;
}

interface DashboardProps {
    objects: Array<{
        id: number;
        name: string;
        description?: string;
        notification_date?: string;
        created_at: string;
        updated_at: string;
        finance_summary: FinanceSummary;
    }>;
}

function formatMoney(value: number): string {
    return value.toLocaleString('ru-RU', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
}

export default function Dashboard({ objects }: DashboardProps) {
    const handleDelete = (id: number) => {
        if (confirm('Вы уверены, что хотите удалить этот объект?')) {
            router.delete(route('object.destroy', id));
        }
    };

    const createObject = () => {
        router.post(route('object.create'));
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Объекты" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight md:text-3xl">Объекты</h1>
                        <p className="mt-1 text-muted-foreground">Управление недвижимостью и показаниями счётчиков</p>
                    </div>
                    <Button onClick={createObject} className="shrink-0">
                        <CirclePlus className="mr-2 h-4 w-4" />
                        Добавить объект
                    </Button>
                </div>

                {objects.length === 0 ? (
                    <Card className="relative min-h-[280px] overflow-hidden border-dashed">
                        <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/10 dark:stroke-neutral-100/10" />
                        <CardContent className="relative flex flex-col items-center justify-center py-16 text-center">
                            <Building2 className="mb-4 h-12 w-12 text-muted-foreground/50" />
                            <p className="text-lg font-medium">Объектов пока нет</p>
                            <p className="mt-1 text-sm text-muted-foreground">Создайте первый объект для учёта показаний</p>
                            <Button onClick={createObject} className="mt-6">
                                <CirclePlus className="mr-2 h-4 w-4" />
                                Создать объект
                            </Button>
                        </CardContent>
                    </Card>
                ) : (
                    <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        {objects.map((object) => (
                            <Card
                                key={object.id}
                                className="flex flex-col border-l-4 border-l-primary/80 transition-shadow hover:shadow-md"
                            >
                                <CardHeader>
                                    <CardTitle className="line-clamp-1">{object.name}</CardTitle>
                                    {object.description ? (
                                        <CardDescription className="line-clamp-2">{object.description}</CardDescription>
                                    ) : (
                                        <CardDescription className="italic">Без описания</CardDescription>
                                    )}
                                </CardHeader>
                                <CardContent className="flex-1 space-y-3">
                                    <p className="text-xs text-muted-foreground">
                                        Создан: {new Date(object.created_at).toLocaleDateString('ru-RU')}
                                    </p>
                                    <div className="rounded-md border border-border/60 bg-muted/30 p-3">
                                        <p className="mb-2 flex items-center gap-1.5 text-xs font-medium text-muted-foreground">
                                            <Wallet className="h-3.5 w-3.5" />
                                            Финансы
                                        </p>
                                        {object.finance_summary.entries_count === 0 ? (
                                            <p className="text-xs text-muted-foreground">Нет записей</p>
                                        ) : (
                                            <div className="space-y-0.5 text-xs">
                                                <p>
                                                    Доходы:{' '}
                                                    <span className="font-medium text-emerald-600 dark:text-emerald-400">
                                                        {formatMoney(object.finance_summary.total_income)}
                                                    </span>
                                                </p>
                                                <p>
                                                    Расходы:{' '}
                                                    <span className="font-medium text-rose-600 dark:text-rose-400">
                                                        {formatMoney(object.finance_summary.total_expense)}
                                                    </span>
                                                </p>
                                                <p className="font-semibold">
                                                    Баланс: {formatMoney(object.finance_summary.balance)}
                                                </p>
                                            </div>
                                        )}
                                        <Link
                                            href={route('object.finances', object.id)}
                                            className="mt-2 inline-block text-xs font-medium text-primary hover:underline"
                                        >
                                            Управление →
                                        </Link>
                                    </div>
                                </CardContent>
                                <CardFooter className="flex gap-2">
                                    <Button asChild className="flex-1">
                                        <Link href={route('object.show', object.id)}>Открыть</Link>
                                    </Button>
                                    <Button
                                        variant="outline"
                                        size="icon"
                                        className="text-destructive hover:bg-destructive/10 hover:text-destructive"
                                        onClick={() => handleDelete(object.id)}
                                    >
                                        <Trash2 className="h-4 w-4" />
                                    </Button>
                                </CardFooter>
                            </Card>
                        ))}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
