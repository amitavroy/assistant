<?php

declare(strict_types=1);

namespace App\Neuron\Workflow;

use App\Neuron\Node\EmailDigestInitialNode;
use App\Neuron\Node\SummariseEmailNode;
use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\Workflow;

class NewsletterDigestWorkflow extends Workflow
{
    /**
     * Returns an array of nodes that make up the workflow.
     *
     * @return Node[]
     */
    protected function nodes(): array
    {
        return [
            new EmailDigestInitialNode,
            new SummariseEmailNode,
        ];
    }
}
