export default function AdminFooter() {
  return (
    <footer className="py-4 px-4 sm:px-6 lg:px-8 border-t border-gray-200 dark:border-gray-700">
      <p className="text-sm text-gray-600 dark:text-gray-400 text-center">
        &copy; {new Date().getFullYear()} Admin Panel. All rights reserved.
      </p>
    </footer>
  );
}

