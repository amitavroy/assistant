import { index } from '@/actions/App/Http/Controllers/NewsletterController';
import Pagination from '@/components/pagination';
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { NewslettersTable } from '@/tables/newsletters-table';
import {
  type BreadcrumbItem,
  type Newsletter,
  type PaginatedData,
} from '@/types';
import { Head } from '@inertiajs/react';

interface NewslettersIndexProps {
  newsletters: PaginatedData<Newsletter>;
}

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Newsletters',
    href: index().url,
  },
];

function NewslettersEmptyState() {
  return (
    <Card>
      <CardHeader>
        <CardTitle>No newsletters found</CardTitle>
        <CardDescription>
          There are no newsletters available at this time.
        </CardDescription>
      </CardHeader>
    </Card>
  );
}

export default function NewslettersIndex({
  newsletters,
}: NewslettersIndexProps) {
  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Newsletters" />
      <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        {newsletters.data.length === 0 ? (
          <NewslettersEmptyState />
        ) : (
          <>
            <Card>
              <CardContent className="p-0">
                <NewslettersTable newsletters={newsletters.data} />
              </CardContent>
            </Card>
            <Pagination links={newsletters.links} />
          </>
        )}
      </div>
    </AppLayout>
  );
}
