import { Button } from '@/components/ui/button';
import { createLearningPath } from '@/routes';
import { Form } from '@inertiajs/react';

interface CreateLearningPathFormProps {
  newsletterId: number;
}

export default function CreateLearningPathForm({
  newsletterId,
}: CreateLearningPathFormProps) {
  return (
    <Form {...createLearningPath.form()}>
      {({ processing }) => (
        <>
          <input type="hidden" name="newsletter_id" value={newsletterId} />
          <Button type="submit" disabled={processing}>
            {processing ? 'Creating...' : 'Get learning path'}
          </Button>
        </>
      )}
    </Form>
  );
}
