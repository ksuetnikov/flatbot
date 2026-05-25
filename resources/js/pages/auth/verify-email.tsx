import { Head, useForm } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';
import { FormEventHandler } from 'react';

import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import AuthLayout from '@/layouts/auth-layout';

export default function VerifyEmail({ status }: { status?: string }) {
    const { post, processing } = useForm({});

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(route('verification.send'));
    };

    return (
        <AuthLayout
            title="Подтвердите email"
            description="Перейдите по ссылке из письма, чтобы активировать аккаунт."
        >
            <Head title="Подтверждение email" />

            {status === 'verification-link-sent' && (
                <div className="text-center text-sm font-medium text-green-600">
                    Новая ссылка отправлена на ваш email.
                </div>
            )}

            <form onSubmit={submit} className="space-y-6 text-center">
                <Button disabled={processing} variant="secondary" className="h-11 w-full">
                    {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
                    Отправить письмо снова
                </Button>

                <TextLink href={route('logout')} method="post" className="mx-auto block text-sm">
                    Выйти
                </TextLink>
            </form>
        </AuthLayout>
    );
}
