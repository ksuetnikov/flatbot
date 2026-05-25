import { SVGAttributes } from 'react';

export default function AppLogoIcon(props: SVGAttributes<SVGElement>) {
    return (
        <svg {...props} viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="4" y="14" width="24" height="14" rx="2" fill="currentColor" fillOpacity="0.9" />
            <path
                d="M16 4L6 12h20L16 4z"
                fill="currentColor"
            />
            <rect x="11" y="18" width="4" height="6" rx="0.5" fill="var(--logo-window, #fff)" fillOpacity="0.95" />
            <rect x="17" y="18" width="4" height="6" rx="0.5" fill="var(--logo-window, #fff)" fillOpacity="0.95" />
            <rect x="13" y="10" width="6" height="4" rx="0.5" fill="var(--logo-window, #fff)" fillOpacity="0.95" />
        </svg>
    );
}
