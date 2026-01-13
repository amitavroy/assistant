<?php

use App\Models\Newsletter;

test('newsletter can be created with all required fields', function () {
    $newsletter = Newsletter::factory()->create([
        'uid' => 'test-uid-123',
        'subject' => 'Test Subject',
        'from' => 'test@example.com',
        'date' => '2024-01-01 12:00:00',
        'content' => 'Test content here',
        'summary' => 'Test summary',
    ]);

    expect($newsletter->uid)->toBe('test-uid-123');
    expect($newsletter->subject)->toBe('Test Subject');
    expect($newsletter->from)->toBe('test@example.com');
    expect($newsletter->date)->toBe('2024-01-01 12:00:00');
    expect($newsletter->content)->toBe('Test content here');
    expect($newsletter->summary)->toBe('Test summary');
});

test('newsletter can be created without summary', function () {
    $newsletter = Newsletter::factory()->create([
        'uid' => 'test-uid-456',
        'subject' => 'Test Subject',
        'from' => 'test@example.com',
        'date' => '2024-01-01 12:00:00',
        'content' => 'Test content here',
        'summary' => null,
    ]);

    expect($newsletter->summary)->toBeNull();
});

test('newsletter factory creates valid newsletter', function () {
    $newsletter = Newsletter::factory()->create();

    expect($newsletter->uid)->not->toBeEmpty();
    expect($newsletter->subject)->not->toBeEmpty();
    expect($newsletter->from)->not->toBeEmpty();
    expect($newsletter->date)->not->toBeEmpty();
    expect($newsletter->content)->not->toBeEmpty();
});

test('newsletter can be updated', function () {
    $newsletter = Newsletter::factory()->create([
        'subject' => 'Original Subject',
    ]);

    $newsletter->update([
        'subject' => 'Updated Subject',
    ]);

    expect($newsletter->fresh()->subject)->toBe('Updated Subject');
});

test('newsletter can be deleted', function () {
    $newsletter = Newsletter::factory()->create();

    $newsletter->delete();

    expect(Newsletter::find($newsletter->id))->toBeNull();
});

test('newsletter has correct fillable attributes', function () {
    $newsletter = new Newsletter;

    $fillable = $newsletter->getFillable();

    expect($fillable)->toContain('job_id');
    expect($fillable)->toContain('uid');
    expect($fillable)->toContain('subject');
    expect($fillable)->toContain('from');
    expect($fillable)->toContain('date');
    expect($fillable)->toContain('content');
    expect($fillable)->toContain('summary');
});

test('newsletter can be created with job_id', function () {
    $jobId = 'test-job-id-123';
    $newsletter = Newsletter::factory()->create([
        'job_id' => $jobId,
        'uid' => 'test-uid-789',
        'subject' => 'Test Subject',
        'from' => 'test@example.com',
        'date' => '2024-01-01 12:00:00',
        'content' => 'Test content here',
    ]);

    expect($newsletter->job_id)->toBe($jobId);
});
