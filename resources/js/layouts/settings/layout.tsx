import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { cn } from '@/lib/utils';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/react';
import { type PropsWithChildren } from 'react';

const sidebarNavItems: NavItem[] = [
    {
        title: 'Профиль',
        href: '/settings/profile',
        icon: null,
    },
    {
        title: 'Пароль',
        href: '/settings/password',
        icon: null,
    },
    {
        title: 'Оформление',
        href: '/settings/appearance',
        icon: null,
    },
];

export default function SettingsLayout({ children }: PropsWithChildren) {
    if (typeof window === 'undefined') {
        return null;
    }

    const currentPath = window.location.pathname;

    return (
        <div className="px-4 py-6 md:px-0">
            <Heading title="Настройки" description="Профиль, безопасность и оформление панели" />

            <div className="mt-6 flex flex-col space-y-8 lg:flex-row lg:space-y-0 lg:space-x-12">
                <aside className="w-full lg:w-52">
                    <nav className="flex flex-row gap-1 overflow-x-auto pb-1 lg:flex-col lg:space-y-1 lg:space-x-0 lg:overflow-visible lg:pb-0">
                        {sidebarNavItems.map((item, index) => (
                            <Button
                                key={`${item.href}-${index}`}
                                size="sm"
                                variant="ghost"
                                asChild
                                className={cn('shrink-0 justify-start lg:w-full', {
                                    'bg-muted font-medium': currentPath === item.href,
                                })}
                            >
                                <Link href={item.href} prefetch>
                                    {item.title}
                                </Link>
                            </Button>
                        ))}
                    </nav>
                </aside>

                <Separator className="lg:hidden" />

                <div className="min-w-0 flex-1 md:max-w-2xl">
                    <section className="max-w-xl space-y-12">{children}</section>
                </div>
            </div>
        </div>
    );
}
