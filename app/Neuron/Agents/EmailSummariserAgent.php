<?php

declare(strict_types=1);

namespace App\Neuron\Agents;

use App\Services\AIProviderService;
use NeuronAI\Agent;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\SystemPrompt;
use NeuronAI\Tools\ToolInterface;
use NeuronAI\Tools\Toolkits\ToolkitInterface;

class EmailSummariserAgent extends Agent
{
    protected function provider(): AIProviderInterface
    {
        return app(AIProviderService::class)
            ->getProvider();
    }

    public function instructions(): string
    {
        return (string) new SystemPrompt(
            background: [
                'You are an AI agent that extracts useful information from emails.',
                'Many emails contain promotional content, ads, subscription pitches, and filler. Your job is to filter these out.',
                'Focus only on the substantive, educational, or actionable content.',
            ],
            steps: [
                'Identify and SKIP: subscription offers, discount promotions, testimonials, calls-to-action, signup links, and marketing language.',
                'Identify the CORE content: any educational material, technical explanations, lists of concepts, tutorials, or genuinely informative sections.',
                'Extract the key points from the core content only.',
            ],
            output: [
                'A summary containing ONLY the substantive content.',
                'If the email contains a list of concepts or definitions, preserve them in the summary.',
                'Ignore all promotional sections entirely - do not mention discounts, subscriptions, or offers.',
                'Output in plain text without markdown or emojis.',
            ],
        );
    }

    /**
     * @return ToolInterface[]|ToolkitInterface[]
     */
    protected function tools(): array
    {
        return [];
    }
}
