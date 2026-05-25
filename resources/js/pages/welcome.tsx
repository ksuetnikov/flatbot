import AppLogoIcon from '@/components/app-logo-icon';
import { Button } from '@/components/ui/button';
import { APP_DESCRIPTION, APP_NAME, APP_TAGLINE } from '@/lib/brand';
import { type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { Building2, Gauge, LayoutDashboard } from 'lucide-react';

const highlights = [
    { icon: Building2, title: 'Объекты', text: 'Квартиры и дома с описанием и настройками' },
    { icon: Gauge, title: 'Показания', text: 'Счётчики, файлы и графики потребления' },
    { icon: LayoutDashboard, title: 'Панель', text: 'Удобный доступ с любого устройства' },
];

export default function Welcome() {
    const { auth } = usePage<SharedData>().props;

    return (
        <>
            <Head title="Главная">
                <link rel="preconnect" href="https://fonts.bunny.net" />
                <link
                    href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700"
                    rel="stylesheet"
                />
            </Head>

            <div className="flex min-h-svh flex-col bg-background">
                <header className="sticky top-0 z-50 border-b border-border/60 bg-background/80 backdrop-blur-md">
                    <div className="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6">
                        <Link href={route('home')} className="flex items-center gap-2.5">
                            <span className="flex size-9 items-center justify-center rounded-lg bg-primary text-primary-foreground shadow-sm">
                                <AppLogoIcon className="size-5" style={{ ['--logo-window' as string]: 'var(--primary-foreground)' }} />
                            </span>
                            <span className="font-semibold tracking-tight">{APP_NAME}</span>
                        </Link>
                        <nav className="flex items-center gap-2 sm:gap-3">
                            {auth.user ? (
                                <Button asChild size="sm">
                                    <Link href={route('dashboard')}>Панель</Link>
                                </Button>
                            ) : (
                                <>
                                    <Button asChild variant="ghost" size="sm">
                                        <Link href={route('login')}>Вход</Link>
                                    </Button>
                                    <Button asChild size="sm">
                                        <Link href={route('register')}>Регистрация</Link>
                                    </Button>
                                </>
                            )}
                        </nav>
                    </div>
                </header>

                <main className="flex flex-1 flex-col">
                    <section className="relative overflow-hidden px-4 py-16 sm:px-6 sm:py-24 lg:py-32">
                        <div className="pointer-events-none absolute inset-0 auth-panel-gradient opacity-[0.07] dark:opacity-[0.15]" />
                        <div className="relative mx-auto max-w-6xl">
                            <div className="mx-auto max-w-2xl text-center lg:mx-0 lg:text-left">
                                <p className="mb-3 inline-flex rounded-full border border-primary/20 bg-primary/5 px-3 py-1 text-xs font-medium text-primary">
                                    {APP_TAGLINE}
                                </p>
                                <h1 className="text-3xl font-bold tracking-tight text-foreground sm:text-4xl lg:text-5xl">
                                    Всё по недвижимости — в одной панели
                                </h1>
                                <p className="mt-4 text-base text-muted-foreground sm:text-lg">{APP_DESCRIPTION}</p>
                                <div className="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center lg:justify-start">
                                    {auth.user ? (
                                        <Button asChild size="lg" className="h-12 px-8">
                                            <Link href={route('dashboard')}>Открыть панель</Link>
                                        </Button>
                                    ) : (
                                        <>
                                            <Button asChild size="lg" className="h-12 px-8">
                                                <Link href={route('register')}>Начать бесплатно</Link>
                                            </Button>
                                            <Button asChild variant="outline" size="lg" className="h-12 px-8">
                                                <Link href={route('login')}>Войти</Link>
                                            </Button>
                                        </>
                                    )}
                                </div>
                            </div>
                        </div>
                    </section>

                    <section className="border-t border-border/60 bg-muted/30 px-4 py-14 sm:px-6">
                        <div className="mx-auto grid max-w-6xl gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            {highlights.map(({ icon: Icon, title, text }) => (
                                <div
                                    key={title}
                                    className="rounded-xl border border-border/80 bg-card p-6 shadow-sm transition-shadow hover:shadow-md"
                                >
                                    <span className="mb-4 flex size-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                        <Icon className="size-5" />
                                    </span>
                                    <h2 className="font-semibold">{title}</h2>
                                    <p className="mt-1 text-sm text-muted-foreground">{text}</p>
                                </div>
                            ))}
                        </div>
                    </section>
                </main>

                <footer className="border-t border-border/60 px-4 py-6 text-center text-xs text-muted-foreground sm:px-6">
                    © {new Date().getFullYear()} {APP_NAME} · {APP_TAGLINE}
                </footer>
            </div>
        </>
    );
}
