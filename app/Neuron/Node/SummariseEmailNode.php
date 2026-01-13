<?php

declare(strict_types=1);

namespace App\Neuron\Node;

use App\Models\Newsletter;
use App\Neuron\Agents\EmailSummariserAgent;
use App\Neuron\Events\SummariseEmailEvent;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\StopEvent;
use NeuronAI\Workflow\WorkflowState;

class SummariseEmailNode extends Node
{
    /**
     * Implement the Node's logic
     */
    public function __invoke(SummariseEmailEvent $event, WorkflowState $state): StopEvent
    {
        logger('SummariseEmailNode');

        $jobId = $state->get('job_id');

        $newsletters = Newsletter::query()
            ->where('job_id', $jobId)
            ->whereNull('summary')
            ->get();

        logger('Found '.$newsletters->count().' emails to summarie');

        $newsletters->each(function (Newsletter $newsletter) {
            logger('Summarising email: '.$newsletter->id);
            $summary = EmailSummariserAgent::make()
                ->chat(new UserMessage($newsletter->content))
                ->getContent();

            $newsletter->summary = $summary;
            $newsletter->save();
        });

        logger('Summarisation done');

        return new StopEvent;
    }
}
