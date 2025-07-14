import AppLayout from '@/layouts/app-layout';
import { Head, Link, useForm } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import log from 'eslint-plugin-react/lib/util/log';
import { useState } from 'react';

interface Subscriber {
    id: number;
    name: string;
    description: string;
}

interface EditObjectProps {
    object: {
        id: number;
        name: string;
        subscribers: Subscriber[];
        notification_date: string;
        description?: string;
    };
    allSubscribers?: Subscriber[];
}

// Редактирование объекта
export default function EditObject(this: any, { object , allSubscribers = [] }: EditObjectProps) {
    console.log('Страница редактирования объекта');
    console.log('Subscribers:');
    console.log(allSubscribers)
    const { data, setData, put, processing, errors } = useForm({
        name: object.name,
        subscribers: object.subscribers.map(s => s.id),
        notification_date: object.notification_date || '',
        description: object.description || '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('object.update', object.id));
    };

    const [newSubscriberName, setNewSubscriberName] = useState('');

    const addSubscriber = (e: React.FormEvent) => {
        e.preventDefault();
        console.log('Новый подписчик:', newSubscriberName);
        // Здесь можно добавить логику для добавления подписчика
        setNewSubscriberName(''); // Очищаем поле после добавления
    };

    const handleSubscriberChange = (id: number) => {
        setData('subscribers',
            data.subscribers.includes(id)
                ? data.subscribers.filter(subId => subId !== id) // удаляем если уже есть
                : [...data.subscribers, id] // добавляем если нет
        );
    };

    return (
        <AppLayout>
            <Head title={`Редактирование: ${object.name}`} />

            <div className="py-6">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <h1 className="text-2xl font-bold text-gray-900">Редактирование объекта</h1>

                    <form onSubmit={handleSubmit} className="mt-6 space-y-6">
                        <div>
                            <label htmlFor="name" className="block text-sm font-medium text-gray-700">
                                Название
                            </label>
                            <input
                                id="name"
                                type="text"
                                value={data.name}
                                onChange={(e) => setData('name', e.target.value)}
                                className="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            />
                            {errors.name && <p className="mt-2 text-sm text-red-600">{errors.name}</p>}
                        </div>

                        <div>
                            <label htmlFor="description" className="block text-sm font-medium text-gray-700">
                                Описание
                            </label>
                            <textarea
                                id="description"
                                rows={4}
                                value={data.description}
                                onChange={(e) => setData('description', e.target.value)}
                                className="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            />
                            {errors.description && <p className="mt-2 text-sm text-red-600">{errors.description}</p>}
                        </div>

                        <div>
                            <label htmlFor="notification_date" className="block text-sm font-medium text-gray-700">
                                Дата уведомлений
                            </label>
                            <input
                                id="notification_date"
                                type="text"
                                value={data.notification_date}
                                onChange={(e) => setData('notification_date', e.target.value)}
                                className="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            />
                            {errors.notification_date && <p className="mt-2 text-sm text-red-600">{errors.notification_date}</p>}
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700">
                                Подписчики
                            </label>
                            <div className="mt-1 space-y-2">
                                {allSubscribers.map(subscriber => (
                                    <div key={subscriber.id} className="flex items-center">
                                        <input
                                            type="checkbox"
                                            id={`subscriber-${subscriber.id}`}
                                            checked={data.subscribers.includes(subscriber.id)}
                                            onChange={() => handleSubscriberChange(subscriber.id)}
                                            className="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        />
                                        <label
                                            htmlFor={`subscriber-${subscriber.id}`}
                                            className="ml-2 block text-sm text-gray-900"
                                        >
                                            {subscriber.name}
                                        </label>
                                    </div>
                                ))}
                            </div>
                            {errors.subscribers && <p className="mt-2 text-sm text-red-600">{errors.subscribers}</p>}
                        </div>
                        <div className="flex space-x-4">
                            <div>
                                <input
                                    id="new_subscriber"
                                    type="text"
                                    value={newSubscriberName}
                                    onChange={(e) => setNewSubscriberName(e.target.value)}
                                    onKeyDown={(e) => {
                                        if (e.key === 'Enter') {
                                            addSubscriber(e);
                                        }
                                    }}
                                    className="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                            </div>
                            <Button
                                onClick={addSubscriber}
                                disabled={processing || !newSubscriberName.trim()}
                            >
                                Добавить
                            </Button>
                        </div>

                        <div className="flex space-x-4">
                            <Button type="submit" disabled={processing}>
                                Сохранить
                            </Button>
                            <Link
                                href={route('dashboard')}
                                className="inline-flex items-center rounded bg-gray-200 px-4 py-2 text-gray-800 hover:bg-gray-300"
                            >
                                Отмена
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}
