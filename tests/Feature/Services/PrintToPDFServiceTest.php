<?php

use App\Models\Newsletter;
use App\PdfType;
use App\Services\PrintToPDFService;

test('service can be instantiated', function () {
    $service = new PrintToPDFService;

    expect($service)->toBeInstanceOf(PrintToPDFService::class);
});

test('service has generatePdf method', function () {
    $service = new PrintToPDFService;

    expect(method_exists($service, 'generatePdf'))->toBeTrue();
});

test('generateFilename returns correct format for Summary type', function () {
    $service = new PrintToPDFService;

    $filename = $service->generateFilename('Test Newsletter Subject', PdfType::Summary);

    expect($filename)->toMatch('/^test-newsletter-subject-summary-\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}\.pdf$/');
    expect($filename)->toContain('test-newsletter-subject');
    expect($filename)->toContain('summary');
    expect($filename)->toEndWith('.pdf');
});

test('generateFilename returns correct format for LearningPath type', function () {
    $service = new PrintToPDFService;

    $filename = $service->generateFilename('My Test Subject', PdfType::LearningPath);

    expect($filename)->toMatch('/^my-test-subject-learning-path-\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}\.pdf$/');
    expect($filename)->toContain('my-test-subject');
    expect($filename)->toContain('learning-path');
    expect($filename)->toEndWith('.pdf');
});

test('generateFilename sanitizes special characters in subject', function () {
    $service = new PrintToPDFService;

    $filename = $service->generateFilename('Test Subject with Special Characters! @#$%', PdfType::Summary);

    expect($filename)->toMatch('/^test-subject-with-special-characters-summary-\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}\.pdf$/');
    expect($filename)->not->toContain('!');
    expect($filename)->not->toContain('@');
    expect($filename)->not->toContain('#');
    expect($filename)->not->toContain('$');
    expect($filename)->not->toContain('%');
});

test('generateFilename handles multiple spaces and converts to single dash', function () {
    $service = new PrintToPDFService;

    $filename = $service->generateFilename('Test    Subject   With   Spaces', PdfType::Summary);

    expect($filename)->toMatch('/^test-subject-with-spaces-summary-\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}\.pdf$/');
    expect($filename)->not->toContain('    ');
});

test('generateFilename trims dashes from beginning and end', function () {
    $service = new PrintToPDFService;

    $filename = $service->generateFilename('!!!Test Subject!!!', PdfType::Summary);

    expect($filename)->toMatch('/^test-subject-summary-\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}\.pdf$/');
    expect($filename)->not->toStartWith('-');
    expect($filename)->toStartWith('test-subject-summary-');
});

test('generateFilename includes timestamp in correct format', function () {
    $service = new PrintToPDFService;

    $beforeTime = now();
    $filename = $service->generateFilename('Test Subject', PdfType::Summary);
    $afterTime = now();

    $timestampMatch = preg_match('/(\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2})\.pdf$/', $filename, $matches);
    expect($timestampMatch)->toBe(1);

    $timestamp = $matches[1];
    $parsedTime = now()->createFromFormat('Y-m-d_H-i-s', $timestamp);

    expect($parsedTime->gte($beforeTime->startOfSecond()))->toBeTrue();
    expect($parsedTime->lte($afterTime->endOfSecond()))->toBeTrue();
});

test('generatePdf returns string for Summary type', function () {
    $newsletter = Newsletter::factory()->create([
        'subject' => 'Test Subject',
        'from' => 'test@example.com',
        'date' => '2024-01-15 10:30:00',
        'summary' => '# Summary Title\n\nThis is the summary content.',
    ]);

    $service = new PrintToPDFService;

    $result = $service->generatePdf($newsletter, PdfType::Summary);

    expect($result)->toBeString();
});

test('generatePdf handles null summary', function () {
    $newsletter = Newsletter::factory()->create([
        'subject' => 'Test Subject',
        'from' => 'test@example.com',
        'date' => '2024-01-15 10:30:00',
        'summary' => null,
    ]);

    $service = new PrintToPDFService;

    $result = $service->generatePdf($newsletter, PdfType::Summary);

    expect($result)->toBeString();
});

test('generatePdf returns string for LearningPath type', function () {
    $newsletter = Newsletter::factory()->create([
        'subject' => 'Test Subject',
        'from' => 'test@example.com',
        'date' => '2024-01-15 10:30:00',
        'learning_path' => '# Learning Path\n\nStep 1: Learn basics\nStep 2: Practice',
    ]);

    $service = new PrintToPDFService;

    $result = $service->generatePdf($newsletter, PdfType::LearningPath);

    expect($result)->toBeString();
});

test('generatePdf handles null learning_path', function () {
    $newsletter = Newsletter::factory()->create([
        'subject' => 'Test Subject',
        'from' => 'test@example.com',
        'date' => '2024-01-15 10:30:00',
        'learning_path' => null,
    ]);

    $service = new PrintToPDFService;

    $result = $service->generatePdf($newsletter, PdfType::LearningPath);

    expect($result)->toBeString();
});
