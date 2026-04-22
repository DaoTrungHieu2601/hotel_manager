import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { Button } from "@/components/ui/button";
import { ChevronLeft, ChevronRight } from "lucide-react";

interface Column<T> {
  key: string;
  header: string;
  render?: (row: T) => React.ReactNode;
}

interface DataTableProps<T> {
  columns: Column<T>[];
  data: T[];
  currentPage?: number;
  lastPage?: number;
  onPageChange?: (page: number) => void;
  emptyMessage?: string;
}

export function DataTable<T extends Record<string, unknown>>({
  columns,
  data,
  currentPage = 1,
  lastPage = 1,
  onPageChange,
  emptyMessage = "Không có dữ liệu",
}: DataTableProps<T>) {
  return (
    <div className="space-y-3">
      <div className="rounded-lg border border-border overflow-hidden">
        <Table>
          <TableHeader>
            <TableRow>
              {columns.map((col) => (
                <TableHead key={col.key}>{col.header}</TableHead>
              ))}
            </TableRow>
          </TableHeader>
          <TableBody>
            {data.length === 0 ? (
              <TableRow>
                <TableCell colSpan={columns.length} className="text-center py-8 text-muted-foreground">
                  {emptyMessage}
                </TableCell>
              </TableRow>
            ) : (
              data.map((row, i) => (
                <TableRow key={i}>
                  {columns.map((col) => (
                    <TableCell key={col.key}>
                      {col.render ? col.render(row) : String(row[col.key] ?? "")}
                    </TableCell>
                  ))}
                </TableRow>
              ))
            )}
          </TableBody>
        </Table>
      </div>
      {lastPage > 1 && onPageChange && (
        <div className="flex items-center justify-end gap-2">
          <Button
            variant="outline"
            size="icon-sm"
            disabled={currentPage <= 1}
            onClick={() => onPageChange(currentPage - 1)}
          >
            <ChevronLeft />
          </Button>
          <span className="text-sm text-muted-foreground">
            {currentPage} / {lastPage}
          </span>
          <Button
            variant="outline"
            size="icon-sm"
            disabled={currentPage >= lastPage}
            onClick={() => onPageChange(currentPage + 1)}
          >
            <ChevronRight />
          </Button>
        </div>
      )}
    </div>
  );
}
