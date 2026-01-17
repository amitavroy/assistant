<?php

use App\Actions\GenerateNewsletterPdfAction;
use App\Models\Newsletter;
use App\Models\User;
use App\PdfType;
use Illuminate\Support\Facades\Storage;

test('guests cannot generate PDF', function () {
    $newsletter = Newsletter::factory()->create();

    $this->get('/generate-newsletter-pdf?newsletter_id='.$newsletter->id.'&pdf_type='.PdfType::Summary->value)
        ->assertRedirect(route('login'));
});

test('authenticated users can generate summary PDF', function () {
    $user = User::factory()->create();
    $newsletter = Newsletter::factory()->create([
        'subject' => 'Test Newsletter',
        'from' => 'test@example.com',
        'date' => '2024-01-01 12:00:00',
        'summary' => '# Summary Title\n\nThis is the summary content.',
    ]);

    // Create a temporary PDF file for testing
    Storage::fake('local');
    $testPdfPath = Storage::path('test-newsletter-summary-2024-01-01_12-00-00.pdf');
    file_put_contents($testPdfPath, '%PDF-1.4 test content');

    // Mock the action to return our test file path
    $mockAction = $this->mock(GenerateNewsletterPdfAction::class);
    $mockAction->shouldReceive('execute')
        ->once()
        ->withArgs(function ($newsletterArg, $typeArg) use ($newsletter) {
            return $newsletterArg->id === $newsletter->id && $typeArg === PdfType::Summary;
        })
        ->andReturn([
            'path' => $testPdfPath,
            'filename' => 'test-newsletter-summary-2024-01-01_12-00-00.pdf',
        ]);

    $this->app->instance(GenerateNewsletterPdfAction::class, $mockAction);

    $response = $this->actingAs($user)
        ->get('/generate-newsletter-pdf?newsletter_id='.$newsletter->id.'&pdf_type='.PdfType::Summary->value);

    $response->assertSuccessful();
    $response->assertDownload();
    expect($response->headers->get('Content-Disposition'))->toContain('test-newsletter-summary-');

    // Cleanup
    if (file_exists($testPdfPath)) {
        unlink($testPdfPath);
    }
});

test('authenticated users can generate learning path PDF', function () {
    $user = User::factory()->create();
    $newsletter = Newsletter::factory()->create([
        'subject' => 'Test Newsletter',
        'from' => 'test@example.com',
        'date' => '2024-01-01 12:00:00',
        'learning_path' => '# Learning Path\n\nStep 1: Learn basics',
    ]);

    // Create a temporary PDF file for testing
    Storage::fake('local');
    $testPdfPath = Storage::path('test-newsletter-learning-path-2024-01-01_12-00-00.pdf');
    file_put_contents($testPdfPath, '%PDF-1.4 test content');

    // Mock the action to return our test file path
    $mockAction = $this->mock(GenerateNewsletterPdfAction::class);
    $mockAction->shouldReceive('execute')
        ->once()
        ->withArgs(function ($newsletterArg, $typeArg) use ($newsletter) {
            return $newsletterArg->id === $newsletter->id && $typeArg === PdfType::LearningPath;
        })
        ->andReturn([
            'path' => $testPdfPath,
            'filename' => 'test-newsletter-learning-path-2024-01-01_12-00-00.pdf',
        ]);

    $this->app->instance(GenerateNewsletterPdfAction::class, $mockAction);

    $response = $this->actingAs($user)
        ->get('/generate-newsletter-pdf?newsletter_id='.$newsletter->id.'&pdf_type='.PdfType::LearningPath->value);

    $response->assertSuccessful();
    $response->assertDownload();
    expect($response->headers->get('Content-Disposition'))->toContain('test-newsletter-learning-path-');

    // Cleanup
    if (file_exists($testPdfPath)) {
        unlink($testPdfPath);
    }
});

test('PDF generation requires newsletter_id', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/generate-newsletter-pdf?pdf_type='.PdfType::Summary->value)
        ->assertSessionHasErrors('newsletter_id');
});

test('PDF generation requires pdf_type', function () {
    $user = User::factory()->create();
    $newsletter = Newsletter::factory()->create();

    $this->actingAs($user)
        ->get('/generate-newsletter-pdf?newsletter_id='.$newsletter->id)
        ->assertSessionHasErrors('pdf_type');
});

test('PDF generation requires valid newsletter_id', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/generate-newsletter-pdf?newsletter_id=99999&pdf_type='.PdfType::Summary->value)
        ->assertSessionHasErrors('newsletter_id');
});

test('PDF generation requires valid pdf_type enum', function () {
    $user = User::factory()->create();
    $newsletter = Newsletter::factory()->create();

    $this->actingAs($user)
        ->get('/generate-newsletter-pdf?newsletter_id='.$newsletter->id.'&pdf_type=invalid_type')
        ->assertSessionHasErrors('pdf_type');
});

test('PDF generation handles null summary', function () {
    $user = User::factory()->create();
    $newsletter = Newsletter::factory()->create([
        'subject' => 'Test Newsletter',
        'summary' => null,
    ]);

    // Create a temporary PDF file for testing
    Storage::fake('local');
    $testPdfPath = Storage::path('test-newsletter-summary-2024-01-01_12-00-00.pdf');
    file_put_contents($testPdfPath, '%PDF-1.4 test content');

    // Mock the action
    $mockAction = $this->mock(GenerateNewsletterPdfAction::class);
    $mockAction->shouldReceive('execute')
        ->once()
        ->andReturn([
            'path' => $testPdfPath,
            'filename' => 'test-newsletter-summary-2024-01-01_12-00-00.pdf',
        ]);

    $this->app->instance(GenerateNewsletterPdfAction::class, $mockAction);

    $response = $this->actingAs($user)
        ->get('/generate-newsletter-pdf?newsletter_id='.$newsletter->id.'&pdf_type='.PdfType::Summary->value);

    $response->assertSuccessful();
    $response->assertDownload();

    // Cleanup
    if (file_exists($testPdfPath)) {
        unlink($testPdfPath);
    }
});

test('PDF generation handles null learning_path', function () {
    $user = User::factory()->create();
    $newsletter = Newsletter::factory()->create([
        'subject' => 'Test Newsletter',
        'learning_path' => null,
    ]);

    // Create a temporary PDF file for testing
    Storage::fake('local');
    $testPdfPath = Storage::path('test-newsletter-learning-path-2024-01-01_12-00-00.pdf');
    file_put_contents($testPdfPath, '%PDF-1.4 test content');

    // Mock the action
    $mockAction = $this->mock(GenerateNewsletterPdfAction::class);
    $mockAction->shouldReceive('execute')
        ->once()
        ->andReturn([
            'path' => $testPdfPath,
            'filename' => 'test-newsletter-learning-path-2024-01-01_12-00-00.pdf',
        ]);

    $this->app->instance(GenerateNewsletterPdfAction::class, $mockAction);

    $response = $this->actingAs($user)
        ->get('/generate-newsletter-pdf?newsletter_id='.$newsletter->id.'&pdf_type='.PdfType::LearningPath->value);

    $response->assertSuccessful();
    $response->assertDownload();

    // Cleanup
    if (file_exists($testPdfPath)) {
        unlink($testPdfPath);
    }
});
