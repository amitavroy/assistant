<?php

declare(strict_types=1);

namespace App\Neuron\Node;

use App\Actions\CreateNewsletterAction;
use App\Data\NewsletterData;
use App\Services\Mail\MailServiceInterface;
use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\StartEvent;
use NeuronAI\Workflow\StopEvent;
use NeuronAI\Workflow\WorkflowState;

class EmailDigestInitialNode extends Node
{
    /**
     * Implement the Node's logic
     */
    public function __invoke(StartEvent $event, WorkflowState $state): StopEvent
    {
        logger('EmailDigestInitialNode');

        $newsletterFolder = config('companion.default_folder');

        $service = app(MailServiceInterface::class);
        $messages = $service->getMessages(
            folderName: $newsletterFolder
        );

        logger('Got email messages');

        $action = app(CreateNewsletterAction::class);

        $messages->each(function (NewsletterData $message) use ($action) {
            $action->execute(newsletter: $message);
        });

        logger('Created newsletters');

        return new StopEvent;
    }
}
