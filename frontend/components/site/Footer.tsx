export default function SiteFooter() {
  return (
    <footer className="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <p className="text-sm text-gray-600 dark:text-gray-400 text-center">
          &copy; {new Date().getFullYear()} MyApp. All rights reserved.
        </p>
      </div>
    </footer>
  );
}

