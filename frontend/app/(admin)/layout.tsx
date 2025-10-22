'use client';

import { useState } from "react";
import AdminHeader from "@/components/admin/Header";
import AdminSidebar from "@/components/admin/Sidebar";

export default function AdminLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const [sidebarOpen, setSidebarOpen] = useState(false);

  return (
    <div className="min-h-screen bg-gray-100 dark:bg-gray-900">
      <AdminHeader onMenuClick={() => setSidebarOpen(!sidebarOpen)} />

      <div className="flex pt-16">
        <AdminSidebar isOpen={sidebarOpen} />

        <main className="flex-1 lg:ml-64">
          <div className="py-6 px-4 sm:px-6 lg:px-8">
            {children}
          </div>
        </main>
      </div>
    </div>
  );
}
