<?php

namespace App\Jobs;

use App\Models\Newsletter;
use App\Neuron\Agents\NewsletterLearningPathAgent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use NeuronAI\Chat\Messages\UserMessage;

class CreateLearningPathJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $newsletterId
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Process the learning path creation
        $newsletter = Newsletter::find($this->newsletterId);

        logger('Creating learning path for newsletter: '.$newsletter->id);

        $message = 'You are given a summarised email. Create a learning path for it. The content is: '.$newsletter->summary;

        $learningPath = NewsletterLearningPathAgent::make()
            ->chat(new UserMessage($message))->getContent();

        logger('Learning path created');

        $newsletter->learning_path = $learningPath;
        $newsletter->save();
    }
}
