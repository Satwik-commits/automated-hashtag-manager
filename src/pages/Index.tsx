import { HashtagManager } from "@/components/HashtagManager";

const Index = () => {
  return (
    <div className="min-h-screen bg-gray-50">
      <div className="container py-8">
        <div className="bg-white rounded-lg shadow-sm border border-wp-border">
          <div className="p-6">
            <div className="flex justify-between items-center mb-6">
              <h1 className="text-2xl font-semibold text-wp-text">Automated HashTag Manager</h1>
            </div>
            <HashtagManager />
          </div>
        </div>
      </div>
    </div>
  );
};

export default Index;