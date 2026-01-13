<?php

declare(strict_types=1);

namespace App\Services;

use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\HttpClientOptions;
use NeuronAI\Providers\Ollama\Ollama;
use NeuronAI\Providers\OpenAI\OpenAI;

class AIProviderService
{
    public function getProvider(): AIProviderInterface
    {
        $provider = config('services.ai_provider', 'openai');

        return match (strtolower($provider)) {
            'ollama' => new Ollama(
                url: config('services.ollama.url'),
                model: config('services.ollama.model'),
                parameters: [],
                httpOptions: new HttpClientOptions(timeout: 240),
            ),
            'openai' => new OpenAI(
                key: config('services.openai.key'),
                model: config('services.openai.chat_model'),
            ),
            default => throw new \InvalidArgumentException("Unsupported AI provider: {$provider}"),
        };
    }
}
