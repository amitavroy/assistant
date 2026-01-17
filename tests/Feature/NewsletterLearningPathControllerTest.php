<?php

use App\Jobs\CreateLearningPathJob;
use App\Models\Newsletter;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

test('guests are redirected to the login page', function () {
    $this->post(route('create-learning-path'), [
        'newsletter_id' => 1,
    ])->assertRedirect(route('login'));
});

test('validation fails when newsletter_id is missing', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('create-learning-path'), [])
        ->assertSessionHasErrors('newsletter_id');
});

test('validation fails when newsletter_id does not exist', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('create-learning-path'), [
            'newsletter_id' => 99999,
        ])
        ->assertSessionHasErrors('newsletter_id');
});

test('job is dispatched when valid newsletter_id is provided', function () {
    Queue::fake();

    $user = User::factory()->create();
    $newsletter = Newsletter::factory()->create();

    $this->actingAs($user)
        ->post(route('create-learning-path'), [
            'newsletter_id' => $newsletter->id,
        ])
        ->assertRedirect();

    Queue::assertPushed(CreateLearningPathJob::class, function ($job) use ($newsletter) {
        return $job->newsletterId === $newsletter->id;
    });
});

test('job is dispatched with correct newsletter_id', function () {
    Queue::fake();

    $user = User::factory()->create();
    $newsletter1 = Newsletter::factory()->create();
    $newsletter2 = Newsletter::factory()->create();

    $this->actingAs($user)
        ->post(route('create-learning-path'), [
            'newsletter_id' => $newsletter1->id,
        ])
        ->assertRedirect();

    Queue::assertPushed(CreateLearningPathJob::class, function ($job) use ($newsletter1, $newsletter2) {
        return $job->newsletterId === $newsletter1->id
            && $job->newsletterId !== $newsletter2->id;
    });
});
