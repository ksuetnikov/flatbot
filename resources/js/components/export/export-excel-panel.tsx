import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { FileSpreadsheet } from 'lucide-react';
import { useMemo, useState } from 'react';

const MONTH_OPTIONS = Array.from({ length: 12 }, (_, i) => ({
    value: i + 1,
    label: new Date(2000, i, 1).toLocaleDateString('ru-RU', { month: 'long' }),
}));

type ExportExcelPanelProps = {
    title?: string;
    description?: string;
    /** Ziggy route name */
    exportRoute: string;
    /** Route parameters for ziggy route() */
    routeParams: Record<string, number | string> | number | string;
};

function buildExportUrl(
    exportRoute: string,
    routeParams: ExportExcelPanelProps['routeParams'],
    fullPeriod: boolean,
    yearFrom: number | '',
    monthFrom: number | '',
    yearTo: number | '',
    monthTo: number | '',
): string {
    const base = route(exportRoute, routeParams);
    if (fullPeriod) {
        return base;
    }

    const params = new URLSearchParams();
    if (yearFrom !== '' && monthFrom !== '') {
        params.set('year_from', String(yearFrom));
        params.set('month_from', String(monthFrom));
    }
    if (yearTo !== '' && monthTo !== '') {
        params.set('year_to', String(yearTo));
        params.set('month_to', String(monthTo));
    }

    const query = params.toString();
    return query ? `${base}?${query}` : base;
}

export function ExportExcelPanel({
    title = 'Выгрузка в Excel',
    description = 'По умолчанию выгружается вся история. В конце файла — итог за период: доход, расход, маржа.',
    exportRoute,
    routeParams,
}: ExportExcelPanelProps) {
    const now = new Date();
    const [fullPeriod, setFullPeriod] = useState(true);
    const [yearFrom, setYearFrom] = useState<number | ''>('');
    const [monthFrom, setMonthFrom] = useState<number | ''>('');
    const [yearTo, setYearTo] = useState<number | ''>('');
    const [monthTo, setMonthTo] = useState<number | ''>('');

    const yearOptions = useMemo(() => {
        const current = now.getFullYear();
        return Array.from({ length: 11 }, (_, i) => current - 5 + i);
    }, [now]);

    const exportUrl = useMemo(
        () =>
            buildExportUrl(
                exportRoute,
                routeParams,
                fullPeriod,
                yearFrom,
                monthFrom,
                yearTo,
                monthTo,
            ),
        [exportRoute, routeParams, fullPeriod, yearFrom, monthFrom, yearTo, monthTo],
    );

    const canExport =
        fullPeriod ||
        (yearFrom !== '' && monthFrom !== '') ||
        (yearTo !== '' && monthTo !== '');

    return (
        <Card>
            <CardHeader>
                <CardTitle className="text-base">{title}</CardTitle>
                <CardDescription>{description}</CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
                <label className="flex cursor-pointer items-center gap-2 text-sm">
                    <input
                        type="checkbox"
                        className="size-4 rounded border-input"
                        checked={fullPeriod}
                        onChange={(e) => setFullPeriod(e.target.checked)}
                    />
                    Весь период (вся история)
                </label>

                {!fullPeriod && (
                    <div className="grid gap-4 sm:grid-cols-2">
                        <div className="space-y-2 rounded-md border border-border/60 p-3">
                            <p className="text-xs font-medium text-muted-foreground">С</p>
                            <div className="grid grid-cols-2 gap-2">
                                <div className="space-y-1">
                                    <Label className="text-xs">Год</Label>
                                    <select
                                        className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs"
                                        value={yearFrom}
                                        onChange={(e) =>
                                            setYearFrom(e.target.value ? Number(e.target.value) : '')
                                        }
                                    >
                                        <option value="">—</option>
                                        {yearOptions.map((y) => (
                                            <option key={y} value={y}>
                                                {y}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                                <div className="space-y-1">
                                    <Label className="text-xs">Месяц</Label>
                                    <select
                                        className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs"
                                        value={monthFrom}
                                        onChange={(e) =>
                                            setMonthFrom(e.target.value ? Number(e.target.value) : '')
                                        }
                                    >
                                        <option value="">—</option>
                                        {MONTH_OPTIONS.map((m) => (
                                            <option key={m.value} value={m.value}>
                                                {m.label}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div className="space-y-2 rounded-md border border-border/60 p-3">
                            <p className="text-xs font-medium text-muted-foreground">По</p>
                            <div className="grid grid-cols-2 gap-2">
                                <div className="space-y-1">
                                    <Label className="text-xs">Год</Label>
                                    <select
                                        className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs"
                                        value={yearTo}
                                        onChange={(e) =>
                                            setYearTo(e.target.value ? Number(e.target.value) : '')
                                        }
                                    >
                                        <option value="">—</option>
                                        {yearOptions.map((y) => (
                                            <option key={y} value={y}>
                                                {y}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                                <div className="space-y-1">
                                    <Label className="text-xs">Месяц</Label>
                                    <select
                                        className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs"
                                        value={monthTo}
                                        onChange={(e) =>
                                            setMonthTo(e.target.value ? Number(e.target.value) : '')
                                        }
                                    >
                                        <option value="">—</option>
                                        {MONTH_OPTIONS.map((m) => (
                                            <option key={m.value} value={m.value}>
                                                {m.label}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                )}

                {canExport ? (
                    <Button asChild>
                        <a href={exportUrl}>
                            <FileSpreadsheet className="mr-2 h-4 w-4" />
                            Скачать Excel
                        </a>
                    </Button>
                ) : (
                    <Button type="button" disabled>
                        <FileSpreadsheet className="mr-2 h-4 w-4" />
                        Скачать Excel
                    </Button>
                )}
            </CardContent>
        </Card>
    );
}
