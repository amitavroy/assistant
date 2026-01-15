import { create, index } from '@/actions/App/Http/Controllers/TaskController';
import Pagination from '@/components/pagination';
import { Button } from '@/components/ui/button';
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { TasksTable } from '@/tables/tasks-table';
import { type BreadcrumbItem, type PaginatedData, type Task } from '@/types';
import { Head, Link, router } from '@inertiajs/react';
import { Plus } from 'lucide-react';

interface TasksIndexProps {
  tasks: PaginatedData<Task>;
  showCompleted: boolean;
}

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Tasks',
    href: index().url,
  },
];

function TasksEmptyState() {
  return (
    <Card>
      <CardHeader>
        <CardTitle>No tasks found</CardTitle>
        <CardDescription>
          {`You don't have any tasks yet. Create your first task to get started.`}
        </CardDescription>
      </CardHeader>
    </Card>
  );
}

export default function TasksIndex({ tasks, showCompleted }: TasksIndexProps) {
  const handleToggleCompleted = (checked: boolean) => {
    router.get(
      index().url,
      { show_completed: checked ? '1' : '0' },
      {
        preserveState: true,
        preserveScroll: true,
      },
    );
  };

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Tasks" />
      <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <div className="flex items-center justify-between">
          <h1 className="text-2xl font-semibold">Tasks</h1>
          <Link href={create().url}>
            <Button>
              <Plus className="h-4 w-4" />
              Create Task
            </Button>
          </Link>
        </div>

        <div className="flex items-center gap-2">
          <Checkbox
            id="show-completed"
            checked={showCompleted}
            onCheckedChange={handleToggleCompleted}
          />
          <Label
            htmlFor="show-completed"
            className="cursor-pointer text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
          >
            Show completed tasks
          </Label>
        </div>

        {tasks.data.length === 0 ? (
          <TasksEmptyState />
        ) : (
          <>
            <Card>
              <CardContent className="p-0">
                <TasksTable tasks={tasks.data} />
              </CardContent>
            </Card>
            <Pagination links={tasks.links} />
          </>
        )}
      </div>
    </AppLayout>
  );
}
