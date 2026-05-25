import { useAppearance } from '@/hooks/use-appearance';
import { cn } from '@/lib/utils';
import { Moon, Sun } from 'lucide-react';

interface ThemeToggleProps {
    className?: string;
}

export function ThemeToggle({ className }: ThemeToggleProps) {
    const { isDark, toggleTheme } = useAppearance();

    return (
        <button
            type="button"
            role="switch"
            aria-checked={isDark}
            aria-label={isDark ? 'Включить светлую тему' : 'Включить тёмную тему'}
            onClick={toggleTheme}
            className={cn(
                'relative inline-flex h-9 w-[4.25rem] shrink-0 items-center rounded-full border border-border bg-muted/80 p-1 transition-colors hover:bg-muted focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background focus-visible:outline-none',
                className,
            )}
        >
            <Sun
                className={cn(
                    'relative z-10 size-4 transition-colors',
                    !isDark ? 'text-amber-600' : 'text-muted-foreground/50',
                )}
                aria-hidden
            />
            <span
                className={cn(
                    'absolute top-1 left-1 size-7 rounded-full bg-background shadow-sm ring-1 ring-border/60 transition-transform duration-200 ease-out',
                    isDark && 'translate-x-[1.85rem]',
                )}
            />
            <Moon
                className={cn(
                    'relative z-10 ml-auto size-4 transition-colors',
                    isDark ? 'text-primary' : 'text-muted-foreground/50',
                )}
                aria-hidden
            />
        </button>
    );
}
