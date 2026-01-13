import {
  index,
  show,
} from '@/actions/App/Http/Controllers/NewsletterController';
import { FormattedDate } from '@/components/formatted-date';
import { MarkdownContent } from '@/components/markdown-content';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem, type Newsletter } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import { useState } from 'react';

interface NewsletterShowProps {
  newsletter: Newsletter;
}

export default function NewsletterShow({ newsletter }: NewsletterShowProps) {
  const [activeTab, setActiveTab] = useState('summary');

  const breadcrumbs: BreadcrumbItem[] = [
    {
      title: 'Newsletters',
      href: index().url,
    },
    {
      title: newsletter.subject,
      href: show(newsletter.id).url,
    },
  ];

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title={newsletter.subject} />
      <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <Card>
          <CardHeader>
            <div className="flex items-start gap-4">
              <Link
                href={index().url}
                className="mt-1 text-muted-foreground hover:text-foreground"
              >
                <ArrowLeft className="h-5 w-5" />
              </Link>
              <div className="flex-1">
                <CardTitle className="mb-2">{newsletter.subject}</CardTitle>
                <div className="space-y-1 text-sm text-muted-foreground">
                  <div>
                    <span className="font-medium">From:</span> {newsletter.from}
                  </div>
                  <div>
                    <span className="font-medium">Date:</span>{' '}
                    <FormattedDate date={newsletter.date} variant="detailed" />
                  </div>
                </div>
              </div>
            </div>
          </CardHeader>
          <Separator />
          <CardContent className="pt-6">
            <Tabs value={activeTab} onValueChange={setActiveTab}>
              <TabsList>
                <TabsTrigger value="summary">Summary</TabsTrigger>
                <TabsTrigger value="content">Content</TabsTrigger>
              </TabsList>
              <TabsContent value="summary" className="mt-4">
                {newsletter.summary ? (
                  <MarkdownContent content={newsletter.summary} />
                ) : (
                  <div className="py-8 text-center text-muted-foreground">
                    No summary available for this newsletter.
                  </div>
                )}
              </TabsContent>
              <TabsContent value="content" className="mt-4">
                <div
                  className="prose prose-sm dark:prose-invert max-w-none"
                  dangerouslySetInnerHTML={{ __html: newsletter.content }}
                />
              </TabsContent>
            </Tabs>
          </CardContent>
        </Card>
      </div>
    </AppLayout>
  );
}
