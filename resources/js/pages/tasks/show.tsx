import {
  addComment,
  index,
  show,
  update,
  updateReminder,
} from '@/actions/App/Http/Controllers/TaskController';
import { FormattedDate } from '@/components/formatted-date';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem, type Task } from '@/types';
import { Form, Head, Link, router } from '@inertiajs/react';
import { ArrowLeft, Edit2, Save, X } from 'lucide-react';
import { useState } from 'react';

interface TaskShowProps {
  task: Task;
}

export default function TaskShow({ task }: TaskShowProps) {
  const [isEditing, setIsEditing] = useState(false);
  const [editDescription, setEditDescription] = useState(task.description);
  const [editDueDate, setEditDueDate] = useState(
    task.due_date ? task.due_date.split('T')[0] : '',
  );
  const [editIsCompleted, setEditIsCompleted] = useState(task.is_completed);

  const breadcrumbs: BreadcrumbItem[] = [
    {
      title: 'Tasks',
      href: index().url,
    },
    {
      title:
        task.description.length > 30
          ? `${task.description.substring(0, 30)}...`
          : task.description,
      href: show(task.id).url,
    },
  ];

  const formatDateTimeLocal = (dateString: string | null): string => {
    if (!dateString) return '';
    const date = new Date(dateString);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    return `${year}-${month}-${day}T${hours}:${minutes}`;
  };

  const [reminderValue, setReminderValue] = useState(
    formatDateTimeLocal(task.next_reminder),
  );

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title={task.description} />
      <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <div className="flex items-center gap-4">
          <Link
            href={index().url}
            className="text-muted-foreground hover:text-foreground"
          >
            <ArrowLeft className="h-5 w-5" />
          </Link>
          <h1 className="text-2xl font-semibold">Task Details</h1>
        </div>

        <Card>
          <CardHeader>
            <div className="flex items-start justify-between">
              <div className="flex-1">
                {isEditing ? (
                  <div className="space-y-4">
                    <div className="grid gap-2">
                      <Label htmlFor="edit-description">Description</Label>
                      <textarea
                        id="edit-description"
                        value={editDescription}
                        onChange={(e) => setEditDescription(e.target.value)}
                        rows={3}
                        className="flex w-full min-w-0 rounded-md border border-input bg-transparent px-3 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none selection:bg-primary selection:text-primary-foreground file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 aria-invalid:border-destructive aria-invalid:ring-destructive/20 md:text-sm dark:aria-invalid:ring-destructive/40"
                      />
                    </div>
                    <div className="grid gap-2">
                      <Label htmlFor="edit-due-date">Due Date</Label>
                      <Input
                        id="edit-due-date"
                        type="date"
                        value={editDueDate}
                        onChange={(e) => setEditDueDate(e.target.value)}
                      />
                    </div>
                    <div className="flex items-center gap-2">
                      <Checkbox
                        id="edit-is-completed"
                        checked={editIsCompleted}
                        onCheckedChange={(checked) =>
                          setEditIsCompleted(checked === true)
                        }
                      />
                      <Label
                        htmlFor="edit-is-completed"
                        className="cursor-pointer text-sm leading-none font-medium"
                      >
                        Mark as completed
                      </Label>
                    </div>
                    <div className="flex gap-2">
                      <Form
                        {...update(task.id).form()}
                        data={{
                          description: editDescription,
                          due_date: editDueDate || null,
                          is_completed: editIsCompleted,
                        }}
                        onSuccess={() => setIsEditing(false)}
                      >
                        <Button type="submit" size="sm">
                          <Save className="h-4 w-4" />
                          Save
                        </Button>
                      </Form>
                      <Button
                        type="button"
                        variant="secondary"
                        size="sm"
                        onClick={() => {
                          setIsEditing(false);
                          setEditDescription(task.description);
                          setEditDueDate(
                            task.due_date ? task.due_date.split('T')[0] : '',
                          );
                          setEditIsCompleted(task.is_completed);
                        }}
                      >
                        <X className="h-4 w-4" />
                        Cancel
                      </Button>
                    </div>
                  </div>
                ) : (
                  <div className="space-y-2">
                    <CardTitle className="text-xl">
                      {task.description}
                    </CardTitle>
                    <div className="space-y-1 text-sm text-muted-foreground">
                      <div>
                        <span className="font-medium">Due Date:</span>{' '}
                        {task.due_date ? (
                          <FormattedDate
                            date={task.due_date}
                            variant="detailed"
                          />
                        ) : (
                          <span>No due date</span>
                        )}
                      </div>
                      <div>
                        <span className="font-medium">Status:</span>{' '}
                        <span
                          className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${
                            task.is_completed
                              ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                              : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
                          }`}
                        >
                          {task.is_completed ? 'Completed' : 'Active'}
                        </span>
                      </div>
                    </div>
                    <Button
                      variant="outline"
                      size="sm"
                      onClick={() => setIsEditing(true)}
                    >
                      <Edit2 className="h-4 w-4" />
                      Edit
                    </Button>
                  </div>
                )}
              </div>
            </div>
          </CardHeader>
          <Separator />
          <CardContent className="pt-6">
            <div className="grid gap-6 md:grid-cols-2">
              {/* Left Column - Comments */}
              <div className="space-y-4">
                <div>
                  <h3 className="mb-4 text-lg font-semibold">Comments</h3>
                  <Form
                    {...addComment.form(task.id)}
                    className="space-y-2"
                    onSuccess={() => {
                      // Form will reset on success
                    }}
                  >
                    {({ processing, errors }) => (
                      <>
                        <div className="grid gap-2">
                          <Label htmlFor="comment">Add Comment</Label>
                          <textarea
                            id="comment"
                            name="comment"
                            required
                            rows={3}
                            className="flex w-full min-w-0 rounded-md border border-input bg-transparent px-3 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none selection:bg-primary selection:text-primary-foreground file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 aria-invalid:border-destructive aria-invalid:ring-destructive/20 md:text-sm dark:aria-invalid:ring-destructive/40"
                            placeholder="Enter a comment..."
                          />
                          <InputError message={errors.comment} />
                        </div>
                        <Button type="submit" disabled={processing} size="sm">
                          {processing ? 'Adding...' : 'Add Comment'}
                        </Button>
                      </>
                    )}
                  </Form>
                </div>

                <Separator />

                <div>
                  <h4 className="mb-2 text-sm font-medium">All Comments</h4>
                  {task.comments.length === 0 ? (
                    <p className="text-sm text-muted-foreground">
                      No comments yet.
                    </p>
                  ) : (
                    <div className="space-y-3">
                      {task.comments.map((comment, index) => (
                        <div
                          key={index}
                          className="rounded-lg border bg-muted/50 p-3 text-sm"
                        >
                          {comment}
                        </div>
                      ))}
                    </div>
                  )}
                </div>
              </div>

              {/* Right Column - Reminder */}
              <div className="space-y-4">
                <div>
                  <h3 className="mb-4 text-lg font-semibold">Next Reminder</h3>
                  <Form
                    {...updateReminder.form(task.id)}
                    className="space-y-4"
                    onSuccess={() => {
                      // Success handled by Inertia
                    }}
                  >
                    {({ processing, errors }) => (
                      <>
                        <div className="grid gap-2">
                          <Label htmlFor="next_reminder">
                            Reminder Date & Time
                          </Label>
                          <Input
                            id="next_reminder"
                            name="next_reminder"
                            type="datetime-local"
                            value={reminderValue}
                            onChange={(e) => setReminderValue(e.target.value)}
                            className="mt-1 block w-full"
                          />
                          <InputError message={errors.next_reminder} />
                          <p className="text-xs text-muted-foreground">
                            Set a date and time to be reminded about this task.
                          </p>
                        </div>
                        <div className="flex gap-2">
                          <Button type="submit" disabled={processing} size="sm">
                            {processing ? 'Saving...' : 'Save Reminder'}
                          </Button>
                          {reminderValue && (
                            <Button
                              type="button"
                              variant="secondary"
                              size="sm"
                              onClick={() => {
                                router.put(
                                  updateReminder.url(task.id),
                                  { next_reminder: null },
                                  {
                                    preserveScroll: true,
                                    onSuccess: () => {
                                      setReminderValue('');
                                    },
                                  },
                                );
                              }}
                            >
                              Clear
                            </Button>
                          )}
                        </div>
                        {task.next_reminder && (
                          <div className="rounded-lg border bg-muted/50 p-3">
                            <p className="text-sm">
                              <span className="font-medium">
                                Current reminder:
                              </span>{' '}
                              <FormattedDate
                                date={task.next_reminder}
                                variant="detailed"
                              />
                            </p>
                          </div>
                        )}
                      </>
                    )}
                  </Form>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </AppLayout>
  );
}
