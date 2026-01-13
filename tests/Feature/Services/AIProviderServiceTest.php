<?php

use App\Services\AIProviderService;
use Illuminate\Support\Facades\Config;
use NeuronAI\Providers\Ollama\Ollama;
use NeuronAI\Providers\OpenAI\OpenAI;

test('returns ollama provider when service provider is set to ollama', function () {
    Config::set('services.ai_provider', 'ollama');
    Config::set('services.ollama.url', 'http://localhost:11434/api');
    Config::set('services.ollama.model', 'test-model');

    $service = new AIProviderService;
    $provider = $service->getProvider();

    expect($provider)->toBeInstanceOf(Ollama::class);
});

test('returns openai provider when service provider is set to openai', function () {
    Config::set('services.ai_provider', 'openai');
    Config::set('services.openai.key', 'test-key');
    Config::set('services.openai.chat_model', 'gpt-4');

    $service = new AIProviderService;
    $provider = $service->getProvider();

    expect($provider)->toBeInstanceOf(OpenAI::class);
});

test('returns ollama provider when service provider is uppercase', function () {
    Config::set('services.ai_provider', 'OLLAMA');
    Config::set('services.ollama.url', 'http://localhost:11434/api');
    Config::set('services.ollama.model', 'test-model');

    $service = new AIProviderService;
    $provider = $service->getProvider();

    expect($provider)->toBeInstanceOf(Ollama::class);
});

test('returns openai provider when service provider is mixed case', function () {
    Config::set('services.ai_provider', 'OpenAI');
    Config::set('services.openai.key', 'test-key');
    Config::set('services.openai.chat_model', 'gpt-4');

    $service = new AIProviderService;
    $provider = $service->getProvider();

    expect($provider)->toBeInstanceOf(OpenAI::class);
});

test('throws exception for unsupported provider', function () {
    Config::set('services.ai_provider', 'unsupported-provider');

    $service = new AIProviderService;

    expect(fn () => $service->getProvider())
        ->toThrow(\InvalidArgumentException::class, 'Unsupported AI provider: unsupported-provider');
});
