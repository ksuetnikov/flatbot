import { cn } from '@/lib/utils';
import { useCallback, useEffect, useRef } from 'react';

const ITEM_HEIGHT = 44;
const VISIBLE_COUNT = 5;
const PADDING_ITEMS = Math.floor(VISIBLE_COUNT / 2);

export interface ScrollPickerItem<T extends string | number = number> {
    value: T;
    label: string;
}

interface ScrollPickerColumnProps<T extends string | number> {
    label: string;
    items: ScrollPickerItem<T>[];
    value: T;
    onChange: (value: T) => void;
    className?: string;
}

export function ScrollPickerColumn<T extends string | number>({
    label,
    items,
    value,
    onChange,
    className,
}: ScrollPickerColumnProps<T>) {
    const scrollRef = useRef<HTMLDivElement>(null);
    const scrollEndTimer = useRef<ReturnType<typeof setTimeout> | null>(null);
    const isSyncingScroll = useRef(false);

    const scrollToValue = useCallback(
        (target: T, smooth = false) => {
            const el = scrollRef.current;
            if (!el) return;
            const index = items.findIndex((item) => item.value === target);
            if (index < 0) return;
            isSyncingScroll.current = true;
            el.scrollTo({ top: index * ITEM_HEIGHT, behavior: smooth ? 'smooth' : 'auto' });
            window.setTimeout(() => {
                isSyncingScroll.current = false;
            }, smooth ? 200 : 0);
        },
        [items],
    );

    useEffect(() => {
        scrollToValue(value);
    }, [value, scrollToValue]);

    const syncValueFromScroll = useCallback(() => {
        const el = scrollRef.current;
        if (!el || isSyncingScroll.current) return;

        const index = Math.round(el.scrollTop / ITEM_HEIGHT);
        const clamped = Math.max(0, Math.min(items.length - 1, index));
        const next = items[clamped].value;
        if (next !== value) {
            onChange(next);
        }
        const targetTop = clamped * ITEM_HEIGHT;
        if (Math.abs(el.scrollTop - targetTop) > 1) {
            isSyncingScroll.current = true;
            el.scrollTo({ top: targetTop, behavior: 'smooth' });
            window.setTimeout(() => {
                isSyncingScroll.current = false;
            }, 150);
        }
    }, [items, onChange, value]);

    const handleScroll = () => {
        if (scrollEndTimer.current) {
            clearTimeout(scrollEndTimer.current);
        }
        scrollEndTimer.current = setTimeout(syncValueFromScroll, 80);
    };

    return (
        <div className={cn('flex flex-col items-center', className)}>
            <span className="mb-2 text-xs font-medium tracking-wide text-muted-foreground uppercase">{label}</span>
            <div
                className="relative rounded-lg border bg-muted/30"
                style={{ height: ITEM_HEIGHT * VISIBLE_COUNT }}
            >
                <div
                    className="pointer-events-none absolute inset-x-0 top-1/2 z-10 -translate-y-1/2 border-y border-primary/30 bg-primary/5"
                    style={{ height: ITEM_HEIGHT }}
                    aria-hidden
                />
                <div
                    className="pointer-events-none absolute inset-x-0 top-0 z-10 h-12 bg-gradient-to-b from-background to-transparent"
                    aria-hidden
                />
                <div
                    className="pointer-events-none absolute inset-x-0 bottom-0 z-10 h-12 bg-gradient-to-t from-background to-transparent"
                    aria-hidden
                />
                <div
                    ref={scrollRef}
                    onScroll={handleScroll}
                    className="h-full overflow-y-auto overscroll-y-contain scroll-smooth [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
                    style={{ scrollSnapType: 'y mandatory' }}
                    role="listbox"
                    aria-label={label}
                >
                    {Array.from({ length: PADDING_ITEMS }).map((_, i) => (
                        <div key={`pad-top-${i}`} style={{ height: ITEM_HEIGHT }} aria-hidden />
                    ))}
                    {items.map((item) => {
                        const selected = item.value === value;
                        return (
                            <button
                                key={String(item.value)}
                                type="button"
                                role="option"
                                aria-selected={selected}
                                onClick={() => {
                                    onChange(item.value);
                                    scrollToValue(item.value, true);
                                }}
                                className={cn(
                                    'flex w-16 shrink-0 cursor-pointer items-center justify-center text-lg transition-colors',
                                    selected ? 'font-semibold text-foreground' : 'text-muted-foreground hover:text-foreground',
                                )}
                                style={{ height: ITEM_HEIGHT, scrollSnapAlign: 'center' }}
                            >
                                {item.label}
                            </button>
                        );
                    })}
                    {Array.from({ length: PADDING_ITEMS }).map((_, i) => (
                        <div key={`pad-bottom-${i}`} style={{ height: ITEM_HEIGHT }} aria-hidden />
                    ))}
                </div>
            </div>
        </div>
    );
}
