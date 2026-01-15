import { store, update } from '@/actions/App/Http/Controllers/TaskController';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type Task } from '@/types';
import { Form } from '@inertiajs/react';
import { useState } from 'react';

interface TaskFormProps {
  task?: Task | null;
  onSuccess?: () => void;
  onCancel?: () => void;
  cancelUrl?: string;
}

export default function TaskForm({
  task,
  onSuccess,
  onCancel,
  cancelUrl,
}: TaskFormProps) {
  const isEditing = task !== null && task !== undefined;
  const formProps = isEditing ? update(task.id).form() : store.form();

  const [editDescription, setEditDescription] = useState(
    task?.description || '',
  );
  const [editDueDate, setEditDueDate] = useState(
    task?.due_date ? task.due_date.split('T')[0] : '',
  );
  const [editIsCompleted, setEditIsCompleted] = useState(
    task?.is_completed || false,
  );

  const textareaClassName =
    'border-input file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground flex w-full min-w-0 rounded-md border bg-transparent px-3 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive';

  return (
    <Form
      {...formProps}
      className="max-w-2xl space-y-6"
      data={
        isEditing
          ? {
              description: editDescription,
              due_date: editDueDate || null,
              is_completed: editIsCompleted,
            }
          : undefined
      }
      onSuccess={onSuccess}
    >
      {({ processing, errors }) => (
        <>
          <div className="grid gap-2">
            <Label htmlFor="description">Description</Label>
            {isEditing ? (
              <textarea
                id="description"
                name="description"
                required
                rows={4}
                value={editDescription}
                onChange={(e) => setEditDescription(e.target.value)}
                className={textareaClassName}
                placeholder="Enter task description..."
              />
            ) : (
              <textarea
                id="description"
                name="description"
                required
                rows={4}
                className={textareaClassName}
                placeholder="Enter task description..."
              />
            )}
            <InputError className="mt-2" message={errors.description} />
          </div>

          <div className="grid gap-2">
            <Label htmlFor="due_date">Due Date (Optional)</Label>
            {isEditing ? (
              <Input
                id="due_date"
                name="due_date"
                type="date"
                value={editDueDate}
                onChange={(e) => setEditDueDate(e.target.value)}
                className="mt-1 block w-full"
              />
            ) : (
              <Input
                id="due_date"
                name="due_date"
                type="date"
                className="mt-1 block w-full"
              />
            )}
            <InputError className="mt-2" message={errors.due_date} />
          </div>

          {isEditing && (
            <div className="flex items-center gap-2">
              <Checkbox
                id="is_completed"
                checked={editIsCompleted}
                onCheckedChange={(checked) =>
                  setEditIsCompleted(checked === true)
                }
              />
              <Label
                htmlFor="is_completed"
                className="cursor-pointer text-sm leading-none font-medium"
              >
                Mark as completed
              </Label>
              <InputError className="mt-2" message={errors.is_completed} />
            </div>
          )}

          <div className="flex gap-4">
            <Button type="submit" disabled={processing}>
              {processing
                ? isEditing
                  ? 'Updating...'
                  : 'Creating...'
                : isEditing
                  ? 'Update Task'
                  : 'Create Task'}
            </Button>
            {onCancel && (
              <Button type="button" variant="secondary" onClick={onCancel}>
                Cancel
              </Button>
            )}
            {cancelUrl && !onCancel && (
              <Button type="button" variant="secondary" asChild>
                <a href={cancelUrl}>Cancel</a>
              </Button>
            )}
          </div>
        </>
      )}
    </Form>
  );
}
