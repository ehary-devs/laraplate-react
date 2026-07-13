import type { Auth } from '@/types/auth';
import type { FlashToast } from '@/types/ui';

declare module 'react' {
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    interface InputHTMLAttributes<T> {
        passwordrules?: string;
    }
}

export type Flash = {
    success?: string | null;
    error?: string | null;
    toast?: FlashToast | null;
};

export type PublicSettings = Record<string, unknown>;

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            auth: Auth;
            settings: { public: PublicSettings };
            flash: Flash;
            sidebarOpen: boolean;
            [key: string]: unknown;
        };
    }
}
