import { AuthBrandPanel } from '@/components/auth-brand-panel';
import AppLogoIcon from '@/components/app-logo-icon';
import { ThemeToggle } from '@/components/theme-toggle';
import { APP_NAME } from '@/lib/brand';
import { Link } from '@inertiajs/react';
import { type PropsWithChildren } from 'react';

interface AuthLayoutProps {
    title?: string;
    description?: string;
}

export default function AuthSplitLayout({ children, title, description }: PropsWithChildren<AuthLayoutProps>) {
    return (
        <div className="grid min-h-svh lg:grid-cols-2">
            <div className="relative hidden lg:block">
                <AuthBrandPanel />
            </div>

            <div className="relative flex flex-col bg-background">
                <div className="flex items-center justify-between p-4 sm:p-6 lg:hidden">
                    <Link href={route('home')} className="flex items-center gap-2.5 font-medium">
                        <span className="flex size-9 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                            <AppLogoIcon className="size-5" style={{ ['--logo-window' as string]: 'var(--primary-foreground)' }} />
                        </span>
                        <span className="font-semibold">{APP_NAME}</span>
                    </Link>
                    <ThemeToggle />
                </div>

                <div className="absolute right-4 top-4 hidden lg:block">
                    <ThemeToggle />
                </div>

                <div className="flex flex-1 flex-col items-center justify-center px-4 pb-8 sm:px-6 lg:px-10">
                    <div className="w-full max-w-[400px] space-y-6">
                        <div className="space-y-2 text-center lg:text-left">
                            <h1 className="text-2xl font-bold tracking-tight text-foreground">{title}</h1>
                            {description && <p className="text-sm text-muted-foreground">{description}</p>}
                        </div>
                        {children}
                    </div>
                </div>
            </div>
        </div>
    );
}
