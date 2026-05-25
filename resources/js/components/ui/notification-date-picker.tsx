import { ScrollPickerColumn, type ScrollPickerItem } from '@/components/ui/scroll-picker-column';
import { Label } from '@/components/ui/label';
import {
    buildMonthlyNotificationLocal,
    DEFAULT_NOTIFICATION_PARTS,
    formatMonthlyNotificationPreview,
    parseMonthlyNotification,
    type MonthlyNotificationParts,
} from '@/lib/notification-schedule';
import { cn } from '@/lib/utils';
import { Bell, X } from 'lucide-react';
import { useMemo } from 'react';
import { Button } from './button';

const DAY_ITEMS: ScrollPickerItem<number>[] = Array.from({ length: 31 }, (_, i) => {
    const day = i + 1;
    return { value: day, label: String(day) };
});

const HOUR_ITEMS: ScrollPickerItem<number>[] = Array.from({ length: 24 }, (_, hour) => ({
    value: hour,
    label: String(hour).padStart(2, '0'),
}));

const MINUTE_ITEMS: ScrollPickerItem<number>[] = Array.from({ length: 60 }, (_, minute) => ({
    value: minute,
    label: String(minute).padStart(2, '0'),
}));

interface NotificationDatePickerProps {
    id: string;
    label: string;
    value: string;
    onChange: (value: string) => void;
    /** Исходная дата объекта — для сохранения года/месяца при отправке на бэк */
    referenceIso?: string | null;
    error?: string;
    className?: string;
    clearable?: boolean;
}

export function NotificationDatePicker({
    id,
    label,
    value,
    onChange,
    referenceIso,
    error,
    className,
    clearable = true,
}: NotificationDatePickerProps) {
    const parts = useMemo(
        () => parseMonthlyNotification(value) ?? DEFAULT_NOTIFICATION_PARTS,
        [value],
    );

    const preview = value ? formatMonthlyNotificationPreview(value) : null;

    const updateParts = (patch: Partial<MonthlyNotificationParts>) => {
        const next: MonthlyNotificationParts = { ...parts, ...patch };
        onChange(buildMonthlyNotificationLocal(next, referenceIso ?? (value || null)));
    };

    const handleClear = () => onChange('');

    const ensureValueOnFirstPick = () => {
        if (!value) {
            onChange(buildMonthlyNotificationLocal(DEFAULT_NOTIFICATION_PARTS, referenceIso));
        }
    };

    return (
        <div className={cn('grid gap-2', className)}>
            <Label htmlFor={id}>{label}</Label>
            <div
                id={id}
                className="flex flex-wrap items-start gap-4 rounded-lg border bg-card p-4 shadow-xs"
                aria-invalid={!!error}
            >
                <Bell className="mt-8 size-5 shrink-0 text-muted-foreground" aria-hidden />
                <div className="flex flex-wrap gap-6" onFocus={ensureValueOnFirstPick}>
                    <ScrollPickerColumn
                        label="Число"
                        items={DAY_ITEMS}
                        value={parts.day}
                        onChange={(day) => {
                            ensureValueOnFirstPick();
                            updateParts({ day });
                        }}
                    />
                    <ScrollPickerColumn
                        label="Час"
                        items={HOUR_ITEMS}
                        value={parts.hour}
                        onChange={(hour) => {
                            ensureValueOnFirstPick();
                            updateParts({ hour });
                        }}
                    />
                    <ScrollPickerColumn
                        label="Мин"
                        items={MINUTE_ITEMS}
                        value={parts.minute}
                        onChange={(minute) => {
                            ensureValueOnFirstPick();
                            updateParts({ minute });
                        }}
                    />
                </div>
                {clearable && value && (
                    <Button type="button" variant="outline" size="sm" onClick={handleClear} className="ml-auto shrink-0">
                        <X className="size-4" />
                        Очистить
                    </Button>
                )}
            </div>
            {preview && <p className="text-sm text-muted-foreground">Уведомление: {preview}</p>}
            {error && <p className="text-sm text-destructive">{error}</p>}
        </div>
    );
}
