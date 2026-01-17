<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Newsletter;
use App\PdfType;
use App\Services\PrintToPDFService;
use Illuminate\Support\Facades\Storage;

class GenerateNewsletterPdfAction
{
    public function __construct(
        private PrintToPDFService $pdfService
    ) {}

    /**
     * Generate PDF for newsletter and return absolute path and filename.
     *
     * @return array{path: string, filename: string}
     */
    public function execute(Newsletter $newsletter, PdfType $pdfType): array
    {
        $filePath = $this->pdfService->generatePdf($newsletter, $pdfType);
        $filename = $this->pdfService->generateFilename($newsletter->subject, $pdfType);

        // Ensure we have an absolute path
        $absolutePath = str_starts_with($filePath, '/') || str_starts_with($filePath, storage_path())
            ? $filePath
            : Storage::path($filePath);

        return [
            'path' => $absolutePath,
            'filename' => $filename,
        ];
    }
}
