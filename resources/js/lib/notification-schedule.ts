export interface MonthlyNotificationParts {
    day: number;
    hour: number;
    minute: number;
}

const pad2 = (n: number) => String(n).padStart(2, '0');

export const DEFAULT_NOTIFICATION_PARTS: MonthlyNotificationParts = {
    day: 1,
    hour: 9,
    minute: 0,
};

/** Разбор сохранённой даты — для UI берём только число месяца и время */
export function parseMonthlyNotification(value: string | null | undefined): MonthlyNotificationParts | null {
    if (!value) {
        return null;
    }

    let d: Date;
    if (value.includes('T') && !value.endsWith('Z') && !/[+-]\d{2}:\d{2}$/.test(value)) {
        const [datePart, timePart] = value.split('T');
        const [y, m, day] = datePart.split('-').map(Number);
        const [hour, minute] = timePart.split(':').map(Number);
        d = new Date(y, m - 1, day, hour, minute);
    } else {
        d = new Date(value);
    }

    if (Number.isNaN(d.getTime())) {
        return null;
    }

    return {
        day: d.getDate(),
        hour: d.getHours(),
        minute: d.getMinutes(),
    };
}

/**
 * Собирает значение для бэкенда (datetime-local).
 * Год и месяц не выбираются в UI — берём из reference или текущие.
 */
export function buildMonthlyNotificationLocal(
    parts: MonthlyNotificationParts,
    referenceIso?: string | null,
): string {
    const now = new Date();
    let year = now.getFullYear();
    let month = now.getMonth();

    if (referenceIso) {
        const ref = new Date(referenceIso);
        if (!Number.isNaN(ref.getTime())) {
            year = ref.getFullYear();
            month = ref.getMonth();
        }
    }

    const maxDay = new Date(year, month + 1, 0).getDate();
    const day = Math.min(Math.max(1, parts.day), maxDay);

    return `${year}-${pad2(month + 1)}-${pad2(day)}T${pad2(parts.hour)}:${pad2(parts.minute)}`;
}

export function formatMonthlyNotificationPreview(value: string): string {
    const parts = parseMonthlyNotification(value);
    if (!parts) {
        return '';
    }
    return `${parts.day}-го числа каждого месяца в ${pad2(parts.hour)}:${pad2(parts.minute)}`;
}

/** @deprecated используйте parseMonthlyNotification — оставлено для property-readings */
export function toDatetimeLocalValue(iso: string | null | undefined): string {
    if (!iso) {
        return '';
    }
    const d = new Date(iso);
    if (Number.isNaN(d.getTime())) {
        return '';
    }
    return buildMonthlyNotificationLocal(
        { day: d.getDate(), hour: d.getHours(), minute: d.getMinutes() },
        iso,
    );
}
