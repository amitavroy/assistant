<?php

namespace App\Http\Controllers;

use App\Actions\GenerateNewsletterPdfAction;
use App\Http\Requests\GenerateNewsletterPdfRequest;
use App\Models\Newsletter;
use App\PdfType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Inertia\Inertia;

class NewsletterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $newsletters = Newsletter::query()
            ->orderBy('date', 'desc')
            ->paginate(15);

        return Inertia::render('Newsletters/index', [
            'newsletters' => $newsletters,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Newsletter $newsletter)
    {
        return Inertia::render('Newsletters/show', [
            'newsletter' => $newsletter,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Newsletter $newsletter)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Newsletter $newsletter)
    {
        //
    }

    /**
     * Generate PDF for newsletter.
     */
    public function generatePdf(GenerateNewsletterPdfRequest $request, GenerateNewsletterPdfAction $action)
    {
        $newsletter = Context::get('newsletter');
        $pdfType = PdfType::from($request->validated()['pdf_type']);

        $result = $action->execute($newsletter, $pdfType);

        return response()->download($result['path'], $result['filename'])->deleteFileAfterSend(true);
    }
}
