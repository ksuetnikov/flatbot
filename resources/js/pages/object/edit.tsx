import AppLayout from '@/layouts/app-layout';
import { Head, Link, useForm } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { useState } from 'react';
import axios from 'axios';

interface Subscriber {
    id: number;
    name: string;
    description?: string;
}

interface ObjectProperty {
    id: number;
    name: string;
    value?: string;
    code: string;
    description?: string;
}

interface EditObjectProps {
    object: {
        id: number;
        name: string;
        subscribers: Subscriber[];
        properties: ObjectProperty[];
        notification_date: string;
        description?: string;
    };
    allSubscribers?: Subscriber[];
    allProperties?: ObjectProperty[];
}

const jsonHeaders = {
    headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
    },
};

const counterInputClass =
    'min-w-[200px] flex-1 rounded-md border-2 border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500';
const counterCodeClass =
    'min-w-[160px] flex-1 rounded-md border-2 border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500';

export default function EditObject({ object, allSubscribers = [], allProperties = [] }: EditObjectProps) {
    const { data, setData, put, processing, errors } = useForm({
        name: object.name,
        notification_date: object.notification_date || '',
        description: object.description || '',
    });

    const [subscribers, setSubscribers] = useState<Subscriber[]>(allSubscribers);
    const [properties, setProperties] = useState<ObjectProperty[]>(allProperties);

    const [newSubscriberName, setNewSubscriberName] = useState('');
    const [isAddingSubscriber, setIsAddingSubscriber] = useState(false);
    const [subscriberError, setSubscriberError] = useState<string | null>(null);
    const [savingSubscriberId, setSavingSubscriberId] = useState<number | null>(null);

    const [newPropertyName, setNewPropertyName] = useState('');
    const [newPropertyCode, setNewPropertyCode] = useState('');
    const [isAddingProperty, setIsAddingProperty] = useState(false);
    const [propertyError, setPropertyError] = useState<string | null>(null);
    const [savingPropertyId, setSavingPropertyId] = useState<number | null>(null);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('object.update', object.id));
    };

    const addSubscriber = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!newSubscriberName.trim()) return;

        setIsAddingSubscriber(true);
        setSubscriberError(null);

        try {
            const response = await axios.post(
                `/objects/${object.id}/subscribers`,
                { name: newSubscriberName },
                jsonHeaders,
            );

            if (response.data?.data) {
                setSubscribers([...subscribers, response.data.data]);
            }
            setNewSubscriberName('');
        } catch (error) {
            if (axios.isAxiosError(error) && error.response?.status === 422) {
                setSubscriberError(error.response.data.errors?.name?.[0] || 'Ошибка валидации');
            } else {
                setSubscriberError('Ошибка при добавлении подписчика');
            }
        } finally {
            setIsAddingSubscriber(false);
        }
    };

    const updateSubscriber = async (subscriber: Subscriber) => {
        if (!subscriber.name.trim()) return;

        setSavingSubscriberId(subscriber.id);
        setSubscriberError(null);

        try {
            const response = await axios.put(
                `/objects/${object.id}/subscribers/${subscriber.id}`,
                { name: subscriber.name },
                jsonHeaders,
            );

            if (response.data?.data) {
                setSubscribers(subscribers.map((s) => (s.id === subscriber.id ? response.data.data : s)));
            }
        } catch {
            setSubscriberError('Ошибка при сохранении подписчика');
        } finally {
            setSavingSubscriberId(null);
        }
    };

    const deleteSubscriber = async (id: number) => {
        try {
            await axios.delete(`/objects/${object.id}/subscribers/${id}`, jsonHeaders);
            setSubscribers(subscribers.filter((s) => s.id !== id));
        } catch {
            setSubscriberError('Ошибка при удалении подписчика');
        }
    };

    const addProperty = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!newPropertyName.trim() || !newPropertyCode.trim()) return;

        setIsAddingProperty(true);
        setPropertyError(null);

        try {
            const response = await axios.post(
                `/objects/${object.id}/properties`,
                { name: newPropertyName, code: newPropertyCode },
                jsonHeaders,
            );

            if (response.data?.data) {
                setProperties([...properties, response.data.data]);
            }
            setNewPropertyName('');
            setNewPropertyCode('');
        } catch (error) {
            if (axios.isAxiosError(error) && error.response?.status === 422) {
                setPropertyError(error.response.data.message || 'Ошибка валидации');
            } else {
                setPropertyError('Ошибка при добавлении счётчика');
            }
        } finally {
            setIsAddingProperty(false);
        }
    };

    const updateProperty = async (property: ObjectProperty) => {
        if (!property.name.trim() || !property.code.trim()) return;

        setSavingPropertyId(property.id);
        setPropertyError(null);

        try {
            const response = await axios.put(
                `/objects/${object.id}/properties/${property.id}`,
                { name: property.name, code: property.code },
                jsonHeaders,
            );

            if (response.data?.data) {
                setProperties(properties.map((p) => (p.id === property.id ? response.data.data : p)));
            }
        } catch {
            setPropertyError('Ошибка при сохранении счётчика');
        } finally {
            setSavingPropertyId(null);
        }
    };

    const deleteProperty = async (id: number) => {
        try {
            await axios.delete(`/objects/${object.id}/properties/${id}`, jsonHeaders);
            setProperties(properties.filter((p) => p.id !== id));
        } catch {
            setPropertyError('Ошибка при удалении счётчика');
        }
    };

    const patchProperty = (id: number, field: 'name' | 'code', value: string) => {
        setProperties(properties.map((p) => (p.id === id ? { ...p, [field]: value } : p)));
    };

    const patchSubscriber = (id: number, name: string) => {
        setSubscribers(subscribers.map((s) => (s.id === id ? { ...s, name } : s)));
    };

    const renderCounterRow = (
        name: string,
        code: string,
        onNameChange: (v: string) => void,
        onCodeChange: (v: string) => void,
        actions: React.ReactNode,
        lastValue?: string,
    ) => (
        <div>
            <div className="flex flex-wrap items-center gap-4">
                <input
                    type="text"
                    value={name}
                    onChange={(e) => onNameChange(e.target.value)}
                    placeholder="Название счётчика"
                    className={counterInputClass}
                />
                <input
                    type="text"
                    value={code}
                    onChange={(e) => onCodeChange(e.target.value)}
                    placeholder="Код счётчика"
                    className={counterCodeClass}
                />
                {actions}
            </div>
            {lastValue && <p className="mt-1 text-sm text-gray-500">Последнее показание: {lastValue}</p>}
        </div>
    );

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
                            {errors.notification_date && (
                                <p className="mt-2 text-sm text-red-600">{errors.notification_date}</p>
                            )}
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700">Подписчики</label>
                            <div className="mt-2 space-y-3">
                                {subscribers.map((subscriber) => (
                                    <div
                                        key={subscriber.id}
                                        className="flex flex-wrap items-center gap-2 rounded-lg border border-gray-200 p-3"
                                    >
                                        <input
                                            type="text"
                                            value={subscriber.name}
                                            onChange={(e) => patchSubscriber(subscriber.id, e.target.value)}
                                            className="min-w-[200px] flex-1 rounded-md border border-gray-300 px-3 py-2 text-sm"
                                        />
                                        <Button
                                            type="button"
                                            size="sm"
                                            disabled={savingSubscriberId === subscriber.id}
                                            onClick={() => updateSubscriber(subscriber)}
                                        >
                                            {savingSubscriberId === subscriber.id ? 'Сохранение…' : 'Сохранить'}
                                        </Button>
                                        <Button
                                            type="button"
                                            size="sm"
                                            variant="destructive"
                                            onClick={() => deleteSubscriber(subscriber.id)}
                                        >
                                            Удалить
                                        </Button>
                                    </div>
                                ))}
                            </div>
                            {subscriberError && <p className="mt-2 text-sm text-red-600">{subscriberError}</p>}
                        </div>

                        <div className="flex space-x-4">
                            <input
                                type="text"
                                value={newSubscriberName}
                                onChange={(e) => setNewSubscriberName(e.target.value)}
                                placeholder="Имя нового подписчика"
                                className="block w-full rounded-md border-2 border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            />
                            <Button type="button" onClick={addSubscriber} disabled={isAddingSubscriber || !newSubscriberName.trim()}>
                                Добавить
                            </Button>
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700">Счётчики (показания)</label>

                            <div className="mt-3 space-y-3">
                                {properties.map((property) => (
                                    <div key={property.id}>
                                        {renderCounterRow(
                                            property.name,
                                            property.code,
                                            (v) => patchProperty(property.id, 'name', v),
                                            (v) => patchProperty(property.id, 'code', v),
                                            <>
                                                <Button
                                                    type="button"
                                                    disabled={savingPropertyId === property.id}
                                                    onClick={() => updateProperty(property)}
                                                >
                                                    {savingPropertyId === property.id ? 'Сохранение…' : 'Сохранить'}
                                                </Button>
                                                <Button
                                                    type="button"
                                                    variant="destructive"
                                                    onClick={() => deleteProperty(property.id)}
                                                >
                                                    Удалить
                                                </Button>
                                            </>,
                                            property.value,
                                        )}
                                    </div>
                                ))}

                                <div className={properties.length > 0 ? 'border-t border-gray-200 pt-4' : ''}>
                                    {renderCounterRow(
                                        newPropertyName,
                                        newPropertyCode,
                                        setNewPropertyName,
                                        setNewPropertyCode,
                                        <Button
                                            type="button"
                                            onClick={addProperty}
                                            disabled={isAddingProperty || !newPropertyName.trim() || !newPropertyCode.trim()}
                                        >
                                            Добавить счётчик
                                        </Button>,
                                    )}
                                </div>
                            </div>

                            {properties.length === 0 && (
                                <p className="mt-2 text-sm text-gray-500">Пока нет счётчиков — добавьте первый в строке ниже.</p>
                            )}
                            {propertyError && <p className="mt-2 text-sm text-red-600">{propertyError}</p>}
                        </div>

                        <div className="flex space-x-4">
                            <Button type="submit" disabled={processing}>
                                Сохранить объект
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
