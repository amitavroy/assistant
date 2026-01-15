import { index } from '@/actions/App/Http/Controllers/TaskController';
import TaskForm from '@/forms/task-form';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Tasks',
    href: index().url,
  },
  {
    title: 'Create Task',
    href: '#',
  },
];

export default function CreateTask() {
  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Create Task" />
      <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <div className="flex items-center gap-4">
          <Link
            href={index().url}
            className="text-muted-foreground hover:text-foreground"
          >
            <ArrowLeft className="h-5 w-5" />
          </Link>
          <h1 className="text-2xl font-semibold">Create Task</h1>
        </div>

        <TaskForm task={null} cancelUrl={index().url} />
      </div>
    </AppLayout>
  );
}
