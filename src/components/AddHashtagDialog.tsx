import { useState } from "react";
import { Dialog, DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { Label } from "@/components/ui/label";
import { useToast } from "@/components/ui/use-toast";

interface AddHashtagDialogProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  onSave: (keyword: string, tags: string[]) => void;
}

export const AddHashtagDialog = ({ open, onOpenChange, onSave }: AddHashtagDialogProps) => {
  const [keyword, setKeyword] = useState("");
  const [tags, setTags] = useState(["", "", "", ""]);
  const { toast } = useToast();

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!keyword.trim()) {
      toast({
        title: "Error",
        description: "Keyword is required",
        variant: "destructive",
      });
      return;
    }

    onSave(keyword, tags);
    onOpenChange(false);
    setKeyword("");
    setTags(["", "", "", ""]);
    
    toast({
      title: "Success",
      description: "Hashtag set added successfully",
    });
  };

  const handleTagChange = (index: number, value: string) => {
    setTags((prev) => {
      const newTags = [...prev];
      newTags[index] = value;
      return newTags;
    });
  };

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="sm:max-w-[425px]">
        <DialogHeader>
          <DialogTitle>Add New Hashtag Set</DialogTitle>
        </DialogHeader>
        <form onSubmit={handleSubmit} className="space-y-4">
          <div className="space-y-2">
            <Label htmlFor="keyword">Keyword</Label>
            <Input
              id="keyword"
              value={keyword}
              onChange={(e) => setKeyword(e.target.value)}
              placeholder="Enter keyword"
              required
            />
          </div>
          {tags.map((tag, index) => (
            <div key={index} className="space-y-2">
              <Label htmlFor={`tag${index + 1}`}>Tag {index + 1}</Label>
              <Input
                id={`tag${index + 1}`}
                value={tag}
                onChange={(e) => handleTagChange(index, e.target.value)}
                placeholder={`Enter tag ${index + 1}`}
              />
            </div>
          ))}
          <div className="flex justify-end space-x-2 pt-4">
            <Button
              type="button"
              variant="outline"
              onClick={() => onOpenChange(false)}
            >
              Cancel
            </Button>
            <Button type="submit" className="bg-wp-primary hover:bg-wp-primary/90">
              Add Hashtag Set
            </Button>
          </div>
        </form>
      </DialogContent>
    </Dialog>
  );
};