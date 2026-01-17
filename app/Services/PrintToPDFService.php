<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Newsletter;
use App\PdfType;
use Spatie\LaravelMarkdown\MarkdownRenderer;
use Spatie\LaravelPdf\Facades\Pdf;

class PrintToPDFService
{
    public function generatePdf(Newsletter $newsletter, PdfType $type): string
    {
        $content = match ($type) {
            PdfType::Summary => $newsletter->summary ?? '',
            PdfType::LearningPath => $newsletter->learning_path ?? '',
        };

        return $this->generateNewsletterPdf($newsletter, $content, $type);
    }

    private function generateNewsletterPdf(Newsletter $newsletter, string $content, PdfType $type): string
    {
        $markdownRenderer = app(MarkdownRenderer::class);
        $contentHtml = $markdownRenderer->toHtml($content);

        $html = sprintf(
            '<h1>%s</h1><br><br><p>%s - %s</p>%s',
            htmlspecialchars($newsletter->subject),
            htmlspecialchars($newsletter->from),
            htmlspecialchars($newsletter->date),
            $contentHtml
        );

        $filename = $this->generateFilename($newsletter->subject, $type);
        $filePath = storage_path('app/' . $filename);

        // Save the PDF - save() expects an absolute path
        Pdf::html($html)->save($filePath);

        // Return the full path to the saved file
        return $filePath;
    }

    public function generateFilename(string $subject, PdfType $type): string
    {
        $sanitized = preg_replace('/[^a-z0-9]+/i', '-', strtolower($subject));
        $sanitized = trim($sanitized, '-');
        $typeValue = str_replace('_', '-', $type->value);
        $timestamp = now()->format('Y-m-d_H-i-s');

        return sprintf('%s-%s-%s.pdf', $sanitized, $typeValue, $timestamp);
    }
}
