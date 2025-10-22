import SiteHeader from "@/components/site/Header";
import SiteFooter from "@/components/site/Footer";

export default function SiteLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <div className="min-h-screen flex flex-col bg-gray-50 dark:bg-gray-900">
      <SiteHeader />
      
      <main className="flex-1">
        {children}
      </main>

      <SiteFooter />
    </div>
  );
}
