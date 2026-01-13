<?php

use App\Actions\CreateNewsletterAction;
use App\Data\NewsletterData;
use App\Models\Newsletter;

test('creates a new newsletter when it does not exist', function () {
    $action = new CreateNewsletterAction;

    $newsletterData = new NewsletterData(
        uid: 'test-uid-123',
        subject: 'Test Subject',
        from: 'test@example.com',
        date: '2024-01-01 12:00:00',
        content: 'Test content',
        summary: 'Test summary',
    );

    $jobId = 'test-job-id-123';
    $newsletter = $action->execute($newsletterData, $jobId);

    expect($newsletter)->toBeInstanceOf(Newsletter::class);
    expect($newsletter->uid)->toBe('test-uid-123');
    expect($newsletter->job_id)->toBe($jobId);
    expect($newsletter->subject)->toBe('Test Subject');
    expect($newsletter->from)->toBe('test@example.com');
    expect(Newsletter::where('uid', 'test-uid-123')->count())->toBe(1);
});

test('returns existing newsletter when called with same uid', function () {
    $existingJobId = 'existing-job-id-123';
    $existingNewsletter = Newsletter::factory()->create([
        'job_id' => $existingJobId,
        'uid' => 'existing-uid-123',
        'subject' => 'Original Subject',
        'from' => 'original@example.com',
        'date' => '2024-01-01 12:00:00',
        'content' => 'Original content',
        'summary' => 'Original summary',
    ]);

    $action = new CreateNewsletterAction;

    $newsletterData = new NewsletterData(
        uid: 'existing-uid-123',
        subject: 'Different Subject',
        from: 'different@example.com',
        date: '2024-01-02 12:00:00',
        content: 'Different content',
        summary: 'Different summary',
    );

    $newJobId = 'new-job-id-456';
    $result = $action->execute($newsletterData, $newJobId);

    expect($result->id)->toBe($existingNewsletter->id);
    expect($result->uid)->toBe('existing-uid-123');
    expect($result->subject)->toBe('Original Subject');
    expect($result->job_id)->toBe($existingJobId);
    expect(Newsletter::where('uid', 'existing-uid-123')->count())->toBe(1);
});

test('creates newsletter without summary when summary is null', function () {
    $action = new CreateNewsletterAction;

    $newsletterData = new NewsletterData(
        uid: 'test-uid-no-summary',
        subject: 'Test Subject',
        from: 'test@example.com',
        date: '2024-01-01 12:00:00',
        content: 'Test content',
        summary: null,
    );

    $jobId = 'test-job-id-no-summary';
    $newsletter = $action->execute($newsletterData, $jobId);

    expect($newsletter->summary)->toBeNull();
    expect($newsletter->job_id)->toBe($jobId);
});
