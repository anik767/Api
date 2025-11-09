'use client';

import React, { useState } from 'react';
import { useRouter } from 'next/navigation';
const API_URL = 'http://localhost:8000';

export default function CreateCategoryPage() {
    const [name, setName] = useState('');
    const [description, setDescription] = useState('');
    const [message, setMessage] = useState<{ type: 'success' | 'error'; text: string } | null>(null);
    const [loading, setLoading] = useState(false);
    const router = useRouter();

    const createCategory = async (e: React.FormEvent) => {
        e.preventDefault();

        setLoading(true);

        const token = localStorage.getItem('auth_token');
        if (!token) {
            setMessage({ type: 'error', text: 'You are not logged in!' });
            setLoading(false);
            return;
        }

        try {
            const res = await fetch(`${API_URL}/api/categories`, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    Authorization: `Bearer ${token}`,
                },
                body: JSON.stringify({
                    name,
                    description: description || null,
                }),
            });

            const data = await res.json();

            if (!res.ok) {
                if (res.status === 401) {
                    setMessage({ type: 'error', text: 'Session expired. Please login again.' });
                    setTimeout(() => router.push('/login'), 2000);
                    return;
                }
                setMessage({ type: 'error', text: data.message || 'Failed to create category' });
                return;
            }

            setMessage({ type: 'success', text: 'Category created successfully!' });
            
            setTimeout(() => {
                router.push('/admin/category');
            }, 1000);

        } catch (err: any) {
            setMessage({ type: 'error', text: 'Error creating category: ' + (err.message || 'Unknown error') });
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="p-8 max-w-4xl mx-auto text-black bg-white rounded-lg shadow-md relative">
            {/* Loading Overlay */}
            {loading && (
                <div className="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-50 rounded-lg">
                    <img
                        src="/loading.gif"
                        alt="Loading"
                        width={100}
                        height={100}
                        className="mx-auto"
                    />
                </div>
            )}

            {/* Message box */}
            {message && (
                <div
                    className={`mb-6 p-4 rounded-lg shadow-md flex items-center justify-between animate-fade-in ${message.type === 'success'
                        ? 'bg-green-50 border-l-4 border-green-500 text-green-800'
                        : 'bg-red-50 border-l-4 border-red-500 text-red-800'
                        }`}
                >
                    <div className="flex items-center">
                        {message.type === 'success' ? (
                            <svg className="w-6 h-6 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        ) : (
                            <svg className="w-6 h-6 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        )}
                        <span className="font-medium">{message.text}</span>
                    </div>
                    <button
                        onClick={() => setMessage(null)}
                        className="ml-4 text-gray-400 hover:text-gray-600 transition-colors"
                    >
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            )}

            <h1 className="text-3xl font-bold mb-6 text-gray-900">Create New Category</h1>

            {/* Form */}
            <form onSubmit={createCategory} className="space-y-6">
                <div>
                    <label htmlFor="name" className="block text-sm font-semibold text-gray-700 mb-2">
                        Category Name <span className="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        value={name}
                        onChange={(e) => setName(e.target.value)}
                        className="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none"
                        placeholder="e.g., Technology, Business, Lifestyle..."
                        required
                    />
                    <p className="mt-1 text-sm text-gray-500">
                        A unique name for this category. The slug will be automatically generated.
                    </p>
                </div>

                <div>
                    <label htmlFor="description" className="block text-sm font-semibold text-gray-700 mb-2">
                        Description <span className="text-gray-400">(Optional)</span>
                    </label>
                    <textarea
                        id="description"
                        value={description}
                        onChange={(e) => setDescription(e.target.value)}
                        className="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none resize-none"
                        rows={4}
                        placeholder="Brief description of this category..."
                    />
                </div>

                <div className="pt-4 flex gap-4">
                    <button
                        type="submit"
                        disabled={loading}
                        className="flex-1 flex justify-center items-center px-6 py-3 border border-transparent text-base font-semibold rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-lg hover:shadow-xl"
                    >
                        {loading ? (
                            <>
                                <svg className="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Creating Category...
                            </>
                        ) : (
                            <>
                                <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
                                </svg>
                                Create Category
                            </>
                        )}
                    </button>
                    <button
                        type="button"
                        onClick={() => router.back()}
                        className="px-6 py-3 border border-gray-300 text-base font-semibold rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-gray-200 transition-all shadow-md hover:shadow-lg"
                    >
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    );
}

