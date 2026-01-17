<?php

namespace App\Http\Controllers;

use App\Jobs\CreateLearningPathJob;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NewsletterLearningPathController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'newsletter_id' => ['required', 'exists:newsletters,id'],
        ]);

        CreateLearningPathJob::dispatch($request->newsletter_id);

        return Inertia::flash('notification', [
            'type' => 'success',
            'message' => 'Learning path creation has been queued.',
            'title' => 'Success',
        ])->back();
    }
}
