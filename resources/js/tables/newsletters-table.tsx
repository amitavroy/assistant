import { show } from '@/actions/App/Http/Controllers/NewsletterController';
import { FormattedDate } from '@/components/formatted-date';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import { type Newsletter } from '@/types';
import { Link } from '@inertiajs/react';

interface NewslettersTableProps {
  newsletters: Newsletter[];
}

export function NewslettersTable({ newsletters }: NewslettersTableProps) {
  return (
    <Table>
      <TableHeader>
        <TableRow>
          <TableHead>Subject</TableHead>
          <TableHead>From</TableHead>
          <TableHead className="text-right">Date</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        {newsletters.map((newsletter) => (
          <TableRow key={newsletter.id}>
            <TableCell>
              <Link
                href={show(newsletter.id).url}
                className="font-medium hover:underline"
              >
                {newsletter.subject}
              </Link>
            </TableCell>
            <TableCell className="text-muted-foreground">
              {newsletter.from}
            </TableCell>
            <TableCell className="text-right text-muted-foreground">
              <FormattedDate date={newsletter.date} variant="relative" />
            </TableCell>
          </TableRow>
        ))}
      </TableBody>
    </Table>
  );
}
