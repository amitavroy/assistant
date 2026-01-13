<?php

namespace App\Http\Controllers;

use App\Jobs\EmailDigestWorkflowJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FetchMailController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        EmailDigestWorkflowJob::dispatch();

        return Inertia::flash('notification', [
            'type' => 'success',
            'message' => 'Email digest job has been queued.',
            'title' => 'Success',
        ])->back();
    }
}
