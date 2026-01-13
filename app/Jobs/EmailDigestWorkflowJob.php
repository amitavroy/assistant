<?php

namespace App\Jobs;

use App\Neuron\Workflow\NewsletterDigestWorkflow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;
use NeuronAI\Workflow\WorkflowState;

class EmailDigestWorkflowJob implements ShouldQueue
{
    use Queueable;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger('EmailDigest');

        $jobId = Str::uuid()->toString();
        $state = new WorkflowState([
            'job_id' => $jobId,
        ]);

        $handler = NewsletterDigestWorkflow::make(
            state: $state
        )->start();

        $handler->getResult();

        logger('EmailDigest completed');
    }
}
