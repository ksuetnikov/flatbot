import AppLogoIcon from '@/components/app-logo-icon';
import { APP_DESCRIPTION, APP_NAME, APP_TAGLINE } from '@/lib/brand';
import { Building2, Gauge, Users } from 'lucide-react';

const features = [
    { icon: Building2, title: 'Объекты', text: 'Квартиры, дома и коммерция в одном каталоге' },
    { icon: Gauge, title: 'Счётчики', text: 'Показания, графики и история по каждому прибору' },
    { icon: Users, title: 'Подписчики', text: 'Уведомления арендаторам и жильцам в Telegram' },
];

export function AuthBrandPanel() {
    return (
        <div className="auth-panel-gradient relative flex h-full flex-col justify-between overflow-hidden p-8 text-white lg:p-10">
            <div className="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full bg-white/5 blur-3xl" />
            <div className="pointer-events-none absolute -bottom-16 -left-16 h-48 w-48 rounded-full bg-accent/20 blur-3xl" />

            <div className="relative z-10">
                <div className="flex items-center gap-3">
                    <div className="flex size-11 items-center justify-center rounded-xl bg-white/15 text-accent shadow-lg backdrop-blur-sm">
                        <AppLogoIcon className="size-6" style={{ ['--logo-window' as string]: '#0f5c54' }} />
                    </div>
                    <div>
                        <p className="text-xl font-bold tracking-tight">{APP_NAME}</p>
                        <p className="text-sm text-white/70">{APP_TAGLINE}</p>
                    </div>
                </div>
            </div>

            <div className="relative z-10 hidden space-y-6 lg:block">
                <p className="max-w-sm text-lg font-medium leading-snug text-white/95">{APP_DESCRIPTION}</p>
                <ul className="space-y-4">
                    {features.map(({ icon: Icon, title, text }) => (
                        <li key={title} className="flex gap-3">
                            <span className="flex size-9 shrink-0 items-center justify-center rounded-lg bg-white/10">
                                <Icon className="size-4 text-accent" />
                            </span>
                            <span>
                                <span className="block text-sm font-semibold">{title}</span>
                                <span className="block text-sm text-white/65">{text}</span>
                            </span>
                        </li>
                    ))}
                </ul>
            </div>

            <p className="relative z-10 hidden text-xs text-white/45 lg:block">
                © {new Date().getFullYear()} {APP_NAME}
            </p>
        </div>
    );
}
