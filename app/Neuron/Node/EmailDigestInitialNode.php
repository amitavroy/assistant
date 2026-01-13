<?php

declare(strict_types=1);

namespace App\Neuron\Node;

use App\Actions\CreateNewsletterAction;
use App\Data\NewsletterData;
use App\Neuron\Events\SummariseEmailEvent;
use App\Services\Mail\MailServiceInterface;
use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\StartEvent;
use NeuronAI\Workflow\WorkflowState;

class EmailDigestInitialNode extends Node
{
    /**
     * Implement the Node's logic
     */
    public function __invoke(StartEvent $event, WorkflowState $state): SummariseEmailEvent
    {
        logger('EmailDigestInitialNode');

        $newsletterFolder = config('companion.default_folder');
        $jobId = $state->get('job_id');

        $service = app(MailServiceInterface::class);
        $messages = $service->getMessages(
            folderName: $newsletterFolder
        );

        logger('Got email messages');

        $action = app(CreateNewsletterAction::class);

        $messages->each(function (NewsletterData $message) use ($action, $jobId) {
            $action->execute(newsletter: $message, jobId: $jobId);
        });

        logger('Created newsletters');

        return new SummariseEmailEvent;
    }
}
