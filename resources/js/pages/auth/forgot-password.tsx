import { Head, useForm } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';
import { FormEventHandler } from 'react';

import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/auth-layout';

export default function ForgotPassword({ status }: { status?: string }) {
    const { data, setData, post, processing, errors } = useForm<Required<{ email: string }>>({
        email: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(route('password.email'));
    };

    return (
        <AuthLayout title="Восстановление пароля" description="Укажите email — мы отправим ссылку для сброса">
            <Head title="Восстановление пароля" />

            {status && <div className="text-center text-sm font-medium text-green-600">{status}</div>}

            <div className="space-y-6">
                <form onSubmit={submit}>
                    <div className="grid gap-2">
                        <Label htmlFor="email">Email</Label>
                        <Input
                            id="email"
                            type="email"
                            name="email"
                            autoComplete="off"
                            value={data.email}
                            autoFocus
                            onChange={(e) => setData('email', e.target.value)}
                            placeholder="name@company.ru"
                            className="h-11"
                        />

                        <InputError message={errors.email} />
                    </div>

                    <div className="mt-6">
                        <Button className="h-11 w-full" disabled={processing}>
                            {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
                            Отправить ссылку
                        </Button>
                    </div>
                </form>

                <div className="text-center text-sm text-muted-foreground">
                    <TextLink href={route('login')}>Вернуться ко входу</TextLink>
                </div>
            </div>
        </AuthLayout>
    );
}
