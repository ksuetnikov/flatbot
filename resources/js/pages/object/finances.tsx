import AppLayout from '@/layouts/app-layout';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/react';
import { ArrowLeft, CircleMinus, CirclePlus, Trash2, Wallet } from 'lucide-react';
import { useEffect, useMemo } from 'react';

interface FinanceEntry {
    id: number;
    day: number;
    amount: string;
    comment: string | null;
}

interface FinanceMonth {
    year: number;
    month: number;
    incomes: FinanceEntry[];
    expenses: FinanceEntry[];
    total_income: number;
    total_expense: number;
    balance: number;
}

interface ObjectFinancesProps {
    object: { id: number; name: string };
    months: FinanceMonth[];
}

const MONTH_OPTIONS = Array.from({ length: 12 }, (_, i) => ({
    value: i + 1,
    label: new Date(2000, i, 1).toLocaleDateString('ru-RU', { month: 'long' }),
}));

function daysInMonth(year: number, month: number): number {
    return new Date(year, month, 0).getDate();
}

function clampDay(day: number, year: number, month: number): number {
    const max = daysInMonth(year, month);
    return Math.min(Math.max(1, day), max);
}

function formatMoney(value: number | string): string {
    const num = Number(value);
    return Number.isNaN(num)
        ? String(value)
        : num.toLocaleString('ru-RU', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
}

function monthLabel(year: number, month: number): string {
    return new Date(year, month - 1, 1).toLocaleDateString('ru-RU', {
        month: 'long',
        year: 'numeric',
    });
}

function dayLabel(day: number, month: number, year: number): string {
    return new Date(year, month - 1, day).toLocaleDateString('ru-RU', {
        day: 'numeric',
        month: 'long',
    });
}

function DaySelect({
    year,
    month,
    value,
    onChange,
}: {
    year: number;
    month: number;
    value: number;
    onChange: (day: number) => void;
}) {
    const maxDay = daysInMonth(year, month);
    const options = useMemo(
        () => Array.from({ length: maxDay }, (_, i) => i + 1),
        [maxDay],
    );

    return (
        <select
            className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs"
            value={clampDay(value, year, month)}
            onChange={(e) => onChange(Number(e.target.value))}
        >
            {options.map((d) => (
                <option key={d} value={d}>
                    {d}
                </option>
            ))}
        </select>
    );
}

function EntryForm({
    objectId,
    year,
    month,
    type,
    label,
    defaultDay,
}: {
    objectId: number;
    year: number;
    month: number;
    type: 'income' | 'expense';
    label: string;
    defaultDay?: number;
}) {
    const form = useForm({
        year,
        month,
        day: defaultDay ?? 1,
        type,
        amount: '',
        comment: '',
    });

    useEffect(() => {
        form.setData('day', clampDay(form.data.day, year, month));
    }, [year, month]);

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.post(route('object.finances.store', objectId), {
            preserveScroll: true,
            onSuccess: () => form.reset('amount', 'comment'),
        });
    };

    return (
        <form onSubmit={submit} className="flex flex-col gap-2">
            <div className="grid gap-2 sm:grid-cols-[5rem_1fr_1fr_auto] sm:items-end">
                <div className="space-y-1">
                    <Label className="text-xs">День</Label>
                    <DaySelect
                        year={year}
                        month={month}
                        value={form.data.day}
                        onChange={(day) => form.setData('day', day)}
                    />
                </div>
                <div className="space-y-1">
                    <Label className="text-xs">{label}</Label>
                    <Input
                        type="number"
                        step="0.01"
                        min="0.01"
                        placeholder="Сумма"
                        value={form.data.amount}
                        onChange={(e) => form.setData('amount', e.target.value)}
                        required
                    />
                </div>
                <div className="space-y-1">
                    <Label className="text-xs">Комментарий</Label>
                    <Input
                        placeholder="Необязательно"
                        value={form.data.comment}
                        onChange={(e) => form.setData('comment', e.target.value)}
                    />
                </div>
                <Button
                    type="submit"
                    size="sm"
                    disabled={form.processing}
                    variant={type === 'income' ? 'default' : 'secondary'}
                    className="sm:mb-0.5"
                >
                    <CirclePlus className="mr-1 h-4 w-4" />
                    Добавить
                </Button>
            </div>
        </form>
    );
}

function EntryList({
    objectId,
    entries,
    year,
    month,
    typeLabel,
    variant,
}: {
    objectId: number;
    entries: FinanceEntry[];
    year: number;
    month: number;
    typeLabel: string;
    variant: 'income' | 'expense';
}) {
    const handleDelete = (entryId: number) => {
        if (!confirm('Удалить эту запись?')) {
            return;
        }
        router.delete(route('object.finances.destroy', [objectId, entryId]), {
            preserveScroll: true,
        });
    };

    if (entries.length === 0) {
        return <p className="text-sm text-muted-foreground">Нет записей</p>;
    }

    return (
        <ul className="space-y-2">
            {entries.map((entry) => (
                <li
                    key={entry.id}
                    className="flex items-start justify-between gap-2 rounded-md border border-border/60 bg-muted/30 px-3 py-2"
                >
                    <div className="min-w-0 flex-1">
                        <p className="text-xs text-muted-foreground">
                            {dayLabel(entry.day, month, year)}
                        </p>
                        <p
                            className={`font-semibold tabular-nums ${variant === 'income' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'}`}
                        >
                            {variant === 'income' ? '+' : '−'}
                            {formatMoney(entry.amount)}
                        </p>
                        {entry.comment && (
                            <p className="mt-0.5 text-sm text-muted-foreground">{entry.comment}</p>
                        )}
                    </div>
                    <Button
                        type="button"
                        variant="ghost"
                        size="icon"
                        className="shrink-0 text-destructive hover:bg-destructive/10 hover:text-destructive"
                        onClick={() => handleDelete(entry.id)}
                        title={`Удалить ${typeLabel.toLowerCase()}`}
                    >
                        <Trash2 className="h-4 w-4" />
                    </Button>
                </li>
            ))}
        </ul>
    );
}

export default function ObjectFinances({ object, months }: ObjectFinancesProps) {
    const now = new Date();
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Объекты', href: '/dashboard' },
        { title: object.name, href: route('object.show', object.id) },
        { title: 'Финансы', href: route('object.finances', object.id) },
    ];

    const newMonthForm = useForm({
        year: now.getFullYear(),
        month: now.getMonth() + 1,
        day: now.getDate(),
        type: 'income' as 'income' | 'expense',
        amount: '',
        comment: '',
    });

    const yearOptions = useMemo(() => {
        const years = new Set<number>([now.getFullYear()]);
        months.forEach((m) => years.add(m.year));
        return Array.from(years).sort((a, b) => b - a);
    }, [months, now]);

    useEffect(() => {
        newMonthForm.setData(
            'day',
            clampDay(newMonthForm.data.day, newMonthForm.data.year, newMonthForm.data.month),
        );
    }, [newMonthForm.data.year, newMonthForm.data.month]);

    const submitNewMonth = (e: React.FormEvent) => {
        e.preventDefault();
        newMonthForm.post(route('object.finances.store', object.id), {
            preserveScroll: true,
            onSuccess: () => newMonthForm.reset('amount', 'comment'),
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Финансы — ${object.name}`} />

            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <Button asChild variant="ghost" size="sm" className="-ml-2 mb-2 w-fit">
                            <Link href={route('object.show', object.id)}>
                                <ArrowLeft className="mr-2 h-4 w-4" />
                                К объекту
                            </Link>
                        </Button>
                        <h1 className="flex items-center gap-2 text-2xl font-bold tracking-tight md:text-3xl">
                            <Wallet className="h-7 w-7 text-primary" />
                            Финансы
                        </h1>
                        <p className="mt-1 text-muted-foreground">{object.name}</p>
                    </div>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle className="text-base">Новая запись</CardTitle>
                        <CardDescription>Укажите дату и добавьте доход или расход</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={submitNewMonth} className="space-y-4">
                            <div className="grid gap-4 sm:grid-cols-3">
                                <div className="space-y-1">
                                    <Label>Год</Label>
                                    <select
                                        className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs"
                                        value={newMonthForm.data.year}
                                        onChange={(e) => newMonthForm.setData('year', Number(e.target.value))}
                                    >
                                        {yearOptions.map((y) => (
                                            <option key={y} value={y}>
                                                {y}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                                <div className="space-y-1">
                                    <Label>Месяц</Label>
                                    <select
                                        className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs"
                                        value={newMonthForm.data.month}
                                        onChange={(e) => newMonthForm.setData('month', Number(e.target.value))}
                                    >
                                        {MONTH_OPTIONS.map((m) => (
                                            <option key={m.value} value={m.value}>
                                                {m.label}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                                <div className="space-y-1">
                                    <Label>День</Label>
                                    <DaySelect
                                        year={newMonthForm.data.year}
                                        month={newMonthForm.data.month}
                                        value={newMonthForm.data.day}
                                        onChange={(day) => newMonthForm.setData('day', day)}
                                    />
                                </div>
                            </div>
                            <div className="flex gap-2">
                                <Button
                                    type="button"
                                    variant={newMonthForm.data.type === 'income' ? 'default' : 'outline'}
                                    size="sm"
                                    onClick={() => newMonthForm.setData('type', 'income')}
                                >
                                    <CirclePlus className="mr-1 h-4 w-4" />
                                    Доход
                                </Button>
                                <Button
                                    type="button"
                                    variant={newMonthForm.data.type === 'expense' ? 'secondary' : 'outline'}
                                    size="sm"
                                    onClick={() => newMonthForm.setData('type', 'expense')}
                                >
                                    <CircleMinus className="mr-1 h-4 w-4" />
                                    Расход
                                </Button>
                            </div>
                            <div className="grid gap-4 sm:grid-cols-2">
                                <div className="space-y-1">
                                    <Label>Сумма</Label>
                                    <Input
                                        type="number"
                                        step="0.01"
                                        min="0.01"
                                        required
                                        value={newMonthForm.data.amount}
                                        onChange={(e) => newMonthForm.setData('amount', e.target.value)}
                                    />
                                </div>
                                <div className="space-y-1">
                                    <Label>Комментарий</Label>
                                    <Input
                                        value={newMonthForm.data.comment}
                                        onChange={(e) => newMonthForm.setData('comment', e.target.value)}
                                    />
                                </div>
                            </div>
                            <Button type="submit" disabled={newMonthForm.processing}>
                                Сохранить
                            </Button>
                        </form>
                    </CardContent>
                </Card>

                {months.length === 0 ? (
                    <Card className="border-dashed">
                        <CardContent className="py-10 text-center text-muted-foreground">
                            Записей пока нет. Добавьте первый доход или расход выше.
                        </CardContent>
                    </Card>
                ) : (
                    <div className="space-y-6">
                        {months.map((monthData) => (
                            <Card key={`${monthData.year}-${monthData.month}`}>
                                <CardHeader>
                                    <div className="flex flex-wrap items-center justify-between gap-2">
                                        <CardTitle className="capitalize">
                                            {monthLabel(monthData.year, monthData.month)}
                                        </CardTitle>
                                        <div className="flex flex-wrap gap-2">
                                            <Badge variant="outline" className="text-emerald-700 dark:text-emerald-400">
                                                Доходы: {formatMoney(monthData.total_income)}
                                            </Badge>
                                            <Badge variant="outline" className="text-rose-700 dark:text-rose-400">
                                                Расходы: {formatMoney(monthData.total_expense)}
                                            </Badge>
                                            <Badge
                                                variant={monthData.balance >= 0 ? 'default' : 'destructive'}
                                            >
                                                Итого: {formatMoney(monthData.balance)}
                                            </Badge>
                                        </div>
                                    </div>
                                </CardHeader>
                                <CardContent className="grid gap-6 md:grid-cols-2">
                                    <div className="space-y-3">
                                        <h3 className="text-sm font-semibold text-emerald-700 dark:text-emerald-400">
                                            Доходы
                                        </h3>
                                        <EntryList
                                            objectId={object.id}
                                            entries={monthData.incomes}
                                            year={monthData.year}
                                            month={monthData.month}
                                            typeLabel="Доход"
                                            variant="income"
                                        />
                                        <EntryForm
                                            objectId={object.id}
                                            year={monthData.year}
                                            month={monthData.month}
                                            type="income"
                                            label="Сумма"
                                            defaultDay={now.getDate()}
                                        />
                                    </div>
                                    <div className="space-y-3">
                                        <h3 className="text-sm font-semibold text-rose-700 dark:text-rose-400">
                                            Расходы
                                        </h3>
                                        <EntryList
                                            objectId={object.id}
                                            entries={monthData.expenses}
                                            year={monthData.year}
                                            month={monthData.month}
                                            typeLabel="Расход"
                                            variant="expense"
                                        />
                                        <EntryForm
                                            objectId={object.id}
                                            year={monthData.year}
                                            month={monthData.month}
                                            type="expense"
                                            label="Сумма"
                                            defaultDay={now.getDate()}
                                        />
                                    </div>
                                </CardContent>
                            </Card>
                        ))}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
