import AppLogoIcon from './app-logo-icon';
import { APP_NAME, APP_TAGLINE } from '@/lib/brand';

export default function AppLogo() {
    return (
        <>
            <div className="flex aspect-square size-8 items-center justify-center rounded-lg bg-sidebar-primary text-sidebar-primary-foreground shadow-sm">
                <AppLogoIcon className="size-5" style={{ ['--logo-window' as string]: 'currentColor' }} />
            </div>
            <div className="ml-2 grid flex-1 text-left text-sm leading-tight">
                <span className="truncate font-semibold tracking-tight">{APP_NAME}</span>
                <span className="truncate text-xs text-sidebar-foreground/70">{APP_TAGLINE}</span>
            </div>
        </>
    );
}
