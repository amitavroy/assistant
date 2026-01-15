import { destroy, show } from '@/actions/App/Http/Controllers/TaskController';
import { FormattedDate } from '@/components/formatted-date';
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import { type Task } from '@/types';
import { Form, Link } from '@inertiajs/react';
import { Trash2 } from 'lucide-react';
import { useState } from 'react';

interface TasksTableProps {
  tasks: Task[];
}

export function TasksTable({ tasks }: TasksTableProps) {
  const [deleteTaskId, setDeleteTaskId] = useState<number | null>(null);
  const taskToDelete = tasks.find((task) => task.id === deleteTaskId);

  return (
    <Table>
      <TableHeader>
        <TableRow>
          <TableHead>Description</TableHead>
          <TableHead>Due Date</TableHead>
          <TableHead>Status</TableHead>
          <TableHead className="text-right">Actions</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        {tasks.map((task) => (
          <TableRow key={task.id}>
            <TableCell>
              <Link
                href={show(task.id).url}
                className="font-medium hover:underline"
              >
                {task.description.length > 50
                  ? `${task.description.substring(0, 50)}...`
                  : task.description}
              </Link>
            </TableCell>
            <TableCell className="text-muted-foreground">
              {task.due_date ? (
                <FormattedDate date={task.due_date} variant="relative" />
              ) : (
                <span className="text-muted-foreground">No due date</span>
              )}
            </TableCell>
            <TableCell>
              <span
                className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${
                  task.is_completed
                    ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                    : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
                }`}
              >
                {task.is_completed ? 'Completed' : 'Active'}
              </span>
            </TableCell>
            <TableCell className="text-right">
              <Dialog
                open={deleteTaskId === task.id}
                onOpenChange={(open) => {
                  if (!open) {
                    setDeleteTaskId(null);
                  }
                }}
              >
                <DialogTrigger asChild>
                  <Button
                    variant="ghost"
                    size="icon"
                    onClick={(e) => {
                      e.preventDefault();
                      e.stopPropagation();
                      setDeleteTaskId(task.id);
                    }}
                  >
                    <Trash2 className="h-4 w-4" />
                    <span className="sr-only">Delete task</span>
                  </Button>
                </DialogTrigger>
                <DialogContent>
                  <DialogHeader>
                    <DialogTitle>Delete Task</DialogTitle>
                    <DialogDescription>
                      Are you sure you want to delete this task? This action
                      cannot be undone.
                    </DialogDescription>
                  </DialogHeader>
                  {taskToDelete && (
                    <div className="py-4">
                      <p className="text-sm text-muted-foreground">
                        <span className="font-medium">Task:</span>{' '}
                        {taskToDelete.description}
                      </p>
                    </div>
                  )}
                  <DialogFooter>
                    <DialogClose asChild>
                      <Button variant="secondary">Cancel</Button>
                    </DialogClose>
                    <Form
                      {...destroy.form(task.id)}
                      onSuccess={() => setDeleteTaskId(null)}
                    >
                      <Button variant="destructive" type="submit">
                        Delete
                      </Button>
                    </Form>
                  </DialogFooter>
                </DialogContent>
              </Dialog>
            </TableCell>
          </TableRow>
        ))}
      </TableBody>
    </Table>
  );
}
