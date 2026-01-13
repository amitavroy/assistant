<?php

use App\Models\Newsletter;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the login page', function () {
    $this->get(route('newsletters.index'))->assertRedirect(route('login'));
});

test('authenticated users can visit the newsletters index page', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('newsletters.index'))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Newsletters/index')
                ->has('newsletters.data')
                ->has('newsletters.links')
        );
});

test('newsletters index page shows paginated newsletters', function () {
    $user = User::factory()->create();
    Newsletter::factory()->count(20)->create();

    $this->actingAs($user)
        ->get(route('newsletters.index'))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Newsletters/index')
                ->has('newsletters.data', 15)
                ->has('newsletters.links')
                ->where('newsletters.per_page', 15)
                ->where('newsletters.total', 20)
        );
});

test('newsletters are ordered by date descending', function () {
    $user = User::factory()->create();

    $oldNewsletter = Newsletter::factory()->create([
        'date' => '2024-01-01 12:00:00',
        'subject' => 'Old Newsletter',
    ]);

    $newNewsletter = Newsletter::factory()->create([
        'date' => '2024-12-31 12:00:00',
        'subject' => 'New Newsletter',
    ]);

    $this->actingAs($user)
        ->get(route('newsletters.index'))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Newsletters/index')
                ->where('newsletters.data.0.subject', 'New Newsletter')
                ->where('newsletters.data.1.subject', 'Old Newsletter')
        );
});

test('newsletters index page shows empty state when no newsletters exist', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('newsletters.index'))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Newsletters/index')
                ->has('newsletters.data', 0)
                ->where('newsletters.total', 0)
        );
});

test('guests are redirected to login when viewing newsletter', function () {
    $newsletter = Newsletter::factory()->create();

    $this->get(route('newsletters.show', $newsletter))
        ->assertRedirect(route('login'));
});

test('authenticated users can view a newsletter', function () {
    $user = User::factory()->create();
    $newsletter = Newsletter::factory()->create([
        'subject' => 'Test Newsletter',
        'from' => 'test@example.com',
        'date' => '2024-01-01 12:00:00',
        'content' => 'Test content',
        'summary' => 'Test summary',
    ]);

    $this->actingAs($user)
        ->get(route('newsletters.show', $newsletter))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Newsletters/show')
                ->where('newsletter.id', $newsletter->id)
                ->where('newsletter.subject', 'Test Newsletter')
                ->where('newsletter.from', 'test@example.com')
                ->where('newsletter.content', 'Test content')
                ->where('newsletter.summary', 'Test summary')
        );
});

test('newsletter show page displays newsletter without summary', function () {
    $user = User::factory()->create();
    $newsletter = Newsletter::factory()->create([
        'subject' => 'Newsletter Without Summary',
        'summary' => null,
    ]);

    $this->actingAs($user)
        ->get(route('newsletters.show', $newsletter))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Newsletters/show')
                ->where('newsletter.id', $newsletter->id)
                ->where('newsletter.subject', 'Newsletter Without Summary')
                ->where('newsletter.summary', null)
        );
});

test('newsletter show returns 404 for non-existent newsletter', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('newsletters.show', 99999))
        ->assertNotFound();
});
