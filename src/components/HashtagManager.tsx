import { useState } from "react";
import { Input } from "@/components/ui/input";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Edit, Search, Trash2 } from "lucide-react";
import { Button } from "@/components/ui/button";
import { EditHashtagDialog } from "./EditHashtagDialog";

interface HashtagSet {
  id: string;
  keyword: string;
  tags: string[];
}

const MOCK_DATA: HashtagSet[] = [
  {
    id: "1",
    keyword: "Neeta Ambani",
    tags: ["Neeta Ambani", "Anil Ambani", "", ""],
  },
  {
    id: "2",
    keyword: "Anant Ambani",
    tags: ["Anant Ambani", "", "", ""],
  },
  {
    id: "3",
    keyword: "Sukhbir Badal",
    tags: ["Sukhbir Badal", "Shiromani AkaliDal", "", ""],
  },
];

export const HashtagManager = () => {
  const [searchTerm, setSearchTerm] = useState("");
  const [editingSet, setEditingSet] = useState<HashtagSet | null>(null);
  const [hashtagSets, setHashtagSets] = useState<HashtagSet[]>(MOCK_DATA);

  const filteredSets = hashtagSets.filter((set) =>
    set.keyword.toLowerCase().includes(searchTerm.toLowerCase())
  );

  const handleDelete = (id: string) => {
    setHashtagSets((prev) => prev.filter((set) => set.id !== id));
  };

  return (
    <div>
      <div className="flex items-center justify-between mb-6">
        <div className="flex items-center space-x-2">
          <p className="text-sm text-gray-600">
            Automated HashTags: {hashtagSets.length}
          </p>
        </div>
        <div className="relative w-64">
          <Search className="absolute left-2 top-2.5 h-4 w-4 text-gray-500" />
          <Input
            placeholder="Search for keyword"
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="pl-8"
          />
        </div>
      </div>

      <div className="border rounded-md">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Keyword 1</TableHead>
              <TableHead>Tag 1</TableHead>
              <TableHead>Tag 2</TableHead>
              <TableHead>Tag 3</TableHead>
              <TableHead>Tag 4</TableHead>
              <TableHead className="w-[100px]">Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {filteredSets.map((set) => (
              <TableRow key={set.id}>
                <TableCell className="font-medium">{set.keyword}</TableCell>
                {set.tags.map((tag, index) => (
                  <TableCell key={index}>{tag}</TableCell>
                ))}
                <TableCell>
                  <div className="flex space-x-2">
                    <Button
                      variant="ghost"
                      size="icon"
                      onClick={() => setEditingSet(set)}
                    >
                      <Edit className="h-4 w-4 text-wp-primary" />
                    </Button>
                    <Button
                      variant="ghost"
                      size="icon"
                      onClick={() => handleDelete(set.id)}
                    >
                      <Trash2 className="h-4 w-4 text-wp-danger" />
                    </Button>
                  </div>
                </TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </div>

      <EditHashtagDialog
        open={!!editingSet}
        onOpenChange={(open) => !open && setEditingSet(null)}
        hashtagSet={editingSet}
        onSave={(updatedSet) => {
          setHashtagSets((prev) =>
            prev.map((set) => (set.id === updatedSet.id ? updatedSet : set))
          );
          setEditingSet(null);
        }}
      />
    </div>
  );
};