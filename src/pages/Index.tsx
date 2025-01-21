import { useState } from "react";
import { HashtagManager } from "@/components/HashtagManager";
import { Button } from "@/components/ui/button";
import { Plus } from "lucide-react";
import { AddHashtagDialog } from "@/components/AddHashtagDialog";

const Index = () => {
  const [isAddDialogOpen, setIsAddDialogOpen] = useState(false);

  return (
    <div className="min-h-screen bg-gray-50">
      <div className="container py-8">
        <div className="bg-white rounded-lg shadow-sm border border-wp-border">
          <div className="p-6">
            <div className="flex justify-between items-center mb-6">
              <h1 className="text-2xl font-semibold text-wp-text">Automated HashTag Manager</h1>
              <Button 
                onClick={() => setIsAddDialogOpen(true)}
                className="bg-wp-primary hover:bg-wp-primary/90"
              >
                <Plus className="w-4 h-4 mr-2" />
                Add New Tag
              </Button>
            </div>
            <HashtagManager />
          </div>
        </div>
      </div>
      <AddHashtagDialog 
        open={isAddDialogOpen} 
        onOpenChange={setIsAddDialogOpen}
      />
    </div>
  );
};

export default Index;