import { useEffect, useState } from "react";
import { Dialog, DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { Label } from "@/components/ui/label";

interface HashtagSet {
  id: string;
  keyword: string;
  tags: string[];
}

interface EditHashtagDialogProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  hashtagSet: HashtagSet | null;
  onSave: (updatedSet: HashtagSet) => void;
}

export const EditHashtagDialog = ({
  open,
  onOpenChange,
  hashtagSet,
  onSave,
}: EditHashtagDialogProps) => {
  const [keyword, setKeyword] = useState("");
  const [tags, setTags] = useState<string[]>(["", "", "", ""]);

  useEffect(() => {
    if (hashtagSet) {
      setKeyword(hashtagSet.keyword);
      setTags(hashtagSet.tags);
    }
  }, [hashtagSet]);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (hashtagSet) {
      onSave({
        ...hashtagSet,
        keyword,
        tags,
      });
    }
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
          <DialogTitle>Edit Hashtag Set</DialogTitle>
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
              Save Changes
            </Button>
          </div>
        </form>
      </DialogContent>
    </Dialog>
  );
};