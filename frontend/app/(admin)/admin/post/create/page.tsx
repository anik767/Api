'use client';

import React, { useState, useEffect } from 'react';
import { useRouter } from 'next/navigation';
const API_URL = process.env.NEXT_PUBLIC_API_URL;

interface Post {
    id: number;
    title: string;
    content: string;
    image: string;
    user?: {
        id: number;
        name: string;
        is_admin: boolean;
        email: string;
    };
}

interface Category {
    id: number;
    name: string;
    slug: string;
    description: string | null;
}

export default function CreatePostPage() {
    const [title, setTitle] = useState(''); // post title input
    const [content, setContent] = useState(''); // post content input
    const [image, setImage] = useState<File | null>(null); // uploaded image file
    const [categoryId, setCategoryId] = useState<string>(''); // selected category
    const [categories, setCategories] = useState<Category[]>([]); // available categories
    const [message, setMessage] = useState<{ type: 'success' | 'error'; text: string } | null>(null);
    const [loading, setLoading] = useState(false); // show loading state
    const router = useRouter();

    // Load categories on component mount
    useEffect(() => {
        const loadCategories = async () => {
            try {
                const res = await fetch(`${API_URL}/api/categories`, {
                    headers: {
                        Accept: 'application/json',
                    },
                });
                const data = await res.json();
                setCategories(data.data || []);
            } catch (error) {
                console.error('Failed to load categories:', error);
            }
        };
        loadCategories();
    }, []);
    // 🔹 Handle create post
    const createPost = async (e: React.FormEvent) => {
        e.preventDefault(); // prevent page reload

        if (!image) {
            setMessage({ type: 'error', text: 'Please select an image file' });
            return;
        }

        setLoading(true);

        const formData = new FormData();
        formData.append('title', title);
        formData.append('content', content);
        formData.append('image', image);
        if (categoryId) {
            formData.append('category_id', categoryId);
        }

        try {
            const res = await fetch(`${API_URL}/api/posts`, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    Authorization: `Bearer ${localStorage.getItem('auth_token')}`,
                },
                body: formData,
            });

            const data = await res.json();

            if (!res.ok) {
                setMessage({ type: 'error', text: data.message || 'Failed to create post' });
                return;
            }

            router.push('/admin/post');

            setMessage({ type: 'success', text: 'Post created successfully!' });
            setTitle('');
            setContent('');
            setImage(null);
            // Reset file input
            const fileInput = document.getElementById('image') as HTMLInputElement;
            if (fileInput) fileInput.value = '';

            // Auto-dismiss success message after 3 seconds
            setTimeout(() => setMessage(null), 3000);
        } catch (err: any) {
            setMessage({ type: 'error', text: 'Error creating post: ' + (err.message || 'Unknown error') });
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

            <h1 className="text-3xl font-bold mb-6 text-gray-900">Create New Post</h1>

            {/* Form */}
            <form onSubmit={createPost} className="space-y-6">
                <div>
                    <label htmlFor="title" className="block text-sm font-semibold text-gray-700 mb-2">
                        Post Title <span className="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="title"
                        value={title}
                        onChange={(e) => setTitle(e.target.value)}
                        className="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none"
                        placeholder="Enter a catchy title..."
                        required
                    />
                </div>

                <div>
                    <label htmlFor="content" className="block text-sm font-semibold text-gray-700 mb-2">
                        Content <span className="text-red-500">*</span>
                    </label>
                    <textarea
                        id="content"
                        value={content}
                        onChange={(e) => setContent(e.target.value)}
                        className="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none resize-none"
                        rows={6}
                        placeholder="Write your post content here..."
                        required
                    />
                </div>

                <div>
                    <label htmlFor="category" className="block text-sm font-semibold text-gray-700 mb-2">
                        Category <span className="text-gray-400">(Optional)</span>
                    </label>
                    <select
                        id="category"
                        value={categoryId}
                        onChange={(e) => setCategoryId(e.target.value)}
                        className="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none"
                    >
                        <option value="">-- Select a category --</option>
                        {categories.map((category) => (
                            <option key={category.id} value={category.id}>
                                {category.name}
                            </option>
                        ))}
                    </select>
                </div>

                <div>
                    <label htmlFor="image" className="block text-sm font-semibold text-gray-700 mb-2">
                        Featured Image <span className="text-red-500">*</span>
                    </label>
                    <div className="relative">
                        <input
                            type="file"
                            id="image"
                            accept="image/*"
                            onChange={(e) => setImage(e.target.files?.[0] || null)}
                            className="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer"
                            required
                        />
                    </div>
                    {image && (
                        <p className="mt-2 text-sm text-green-600 flex items-center">
                            <svg className="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                            </svg>
                            {image.name} selected
                        </p>
                    )}
                </div>

                <div className="pt-4">
                    <button
                        type="submit"
                        disabled={loading}
                        className="w-full flex justify-center items-center px-6 py-3 border border-transparent text-base font-semibold rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-lg hover:shadow-xl"
                    >
                        {loading ? (
                            <>
                                <svg className="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Creating Post...
                            </>
                        ) : (
                            <>
                                <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
                                </svg>
                                Create Post
                            </>
                        )}
                    </button>
                </div>
            </form>
        </div>
    );
}
