'use client';

import Link from "next/link";
import { useRouter } from "next/navigation";
import { useState, useEffect } from "react";

export default function SiteHeader() {
  const router = useRouter();
  const [isLoggedIn, setIsLoggedIn] = useState(false);

  useEffect(() => {
    // Check if user is logged in
    const token = localStorage.getItem('token');
    setIsLoggedIn(!!token);
  }, []);

  const handleLogout = () => {
    // Clear localStorage
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    
    // Clear remembered credentials
    localStorage.removeItem('rememberedEmail');
    localStorage.removeItem('rememberedPassword');
    
    // Clear cookie
    document.cookie = 'token=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT';
    
    setIsLoggedIn(false);
    
    // Redirect to login
    router.push('/login');
  };
  return (
    <header className="bg-white dark:bg-gray-800 shadow-sm">
      <nav className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between h-16">
          <div className="flex items-center">
            <Link href="/" className="shrink-0 flex items-center">
              <span className="text-xl font-bold text-gray-900 dark:text-white">
                MyApp
              </span>
            </Link>
            
            {/* Navigation Links */}
            <div className="hidden sm:ml-6 sm:flex sm:space-x-8">
                <Link
                  href="/"
                  className="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-900 dark:text-white border-b-2 border-indigo-500"
                >
                  Home
                </Link>
              
                <Link
                  href="/post"
                  className="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-900 dark:text-white border-b-2 border-indigo-500"
                >
                  Post
                </Link>
              
            </div>
          </div>

          {/* Right side buttons */}
          <div className="flex items-center gap-2">
            {isLoggedIn ? (
              <>
                <Link
                  href="/admin"
                  className="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-white"
                >
                  Dashboard
                </Link>
                <button
                  onClick={handleLogout}
                  className="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 transition-colors"
                >
                  Logout
                </button>
              </>
            ) : (
              <>
                <Link
                  href="/login"
                  className="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-white"
                >
                  Sign in
                </Link>
                <Link
                  href="/admin"
                  className="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 transition-colors"
                >
                  Admin
                </Link>
              </>
            )}
          </div>
        </div>
      </nav>
    </header>
  );
}

