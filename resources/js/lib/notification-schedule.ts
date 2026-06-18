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

const WALL_CLOCK_RE = /^(\d{4})-(\d{2})-(\d{2})[T ](\d{2}):(\d{2})/;

/** День и время из строки БД/API без учёта часового пояса */
export function extractWallClock(value: string): MonthlyNotificationParts | null {
    const match = value.match(WALL_CLOCK_RE);
    if (!match) {
        return null;
    }

    return {
        day: Number(match[3]),
        hour: Number(match[4]),
        minute: Number(match[5]),
    };
}

/** Разбор сохранённой даты — для UI берём только число месяца и время */
export function parseMonthlyNotification(value: string | null | undefined): MonthlyNotificationParts | null {
    if (!value) {
        return null;
    }

    return extractWallClock(value);
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

    const ym = referenceIso?.match(/^(\d{4})-(\d{2})/);
    if (ym) {
        year = Number(ym[1]);
        month = Number(ym[2]) - 1;
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

    const parts = parseMonthlyNotification(iso);
    if (!parts) {
        return '';
    }

    return buildMonthlyNotificationLocal(parts, iso);
}
