import AppLayout from '@/layouts/app-layout';
import { Head, Link, useForm } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { useState } from 'react';
import axios from 'axios';

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

    // const addSubscriber = (e: React.FormEvent) => {
    //     e.preventDefault();
    //     console.log('Новый подписчик:', newSubscriberName);
    //
    //     // Здесь можно добавить логику для добавления подписчика
    //     setNewSubscriberName(''); // Очищаем поле после добавления
    // };
    const [newSubscriberName, setNewSubscriberName] = useState('');
    const [isAddingSubscriber, setIsAddingSubscriber] = useState(false);
    const [subscriberError, setSubscriberError] = useState<string | null>(null);

    const addSubscriber = async (e: React.FormEvent) => {
        e.preventDefault();

        if (!newSubscriberName.trim()) return;

        setIsAddingSubscriber(true);
        setSubscriberError(null);

        try {
            // Формируем данные в формате, который ожидает DTO
            const requestData = {
                name: newSubscriberName
            };

            const response = await axios.post(
                `/objects/${object.id}/subscribers`,
                requestData,
                {
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                }
            );
            console.log('response is:')
            console.log(response.data?.data?.id)
            // Обновляем список подписчиков, если бэкенд возвращает нового подписчика
            if (response.data?.data?.id) {
                setData('subscribers', [...data.subscribers, response.data.data.id]);
                // Если нужно обновить allSubscribers, можно добавить:
                // setAllSubscribers([...allSubscribers, response.data.data]);
            }

            setNewSubscriberName('');
        } catch (error) {
            console.error('Ошибка при добавлении подписчика:', error);

            if (axios.isAxiosError(error)) {
                // Обработка ошибок валидации Laravel
                if (error.response?.status === 422) {
                    setSubscriberError(error.response.data.errors.name[0]);
                } else {
                    setSubscriberError(error.response?.data?.message || 'Ошибка при добавлении подписчика');
                }
            } else {
                setSubscriberError('Произошла неизвестная ошибка');
            }
        } finally {
            setIsAddingSubscriber(false);
        }
    };

    const handleSubscriberChange = async (id: number) => {
            const response = await axios.delete(
                `/objects/${object.id}/subscribers/${id}`
            );
        if (response.data?.data?.id) {
            setData('subscribers', [...data.subscribers, response.data.data.id]);
            console.log('deleted')
        }

        try {
            const response = await axios.delete(
                `/objects/${object.id}/subscribers/${id}`
            );

            // Обновляем список подписчиков, если бэкенд возвращает нового подписчика
            if (response.status == 204) {

                setData('subscribers', [...data.subscribers, id]);
            }

            setNewSubscriberName('');
        } catch (error) {
            console.error('Ошибка при добавлении подписчика:', error);

            if (axios.isAxiosError(error)) {
                // Обработка ошибок валидации Laravel
                if (error.response?.status === 422) {
                    setSubscriberError(error.response.data.errors.name[0]);
                } else {
                    setSubscriberError(error.response?.data?.message || 'Ошибка при добавлении подписчика');
                }
            } else {
                setSubscriberError('Произошла неизвестная ошибка');
            }
        } finally {
            setIsAddingSubscriber(false);
        }

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
