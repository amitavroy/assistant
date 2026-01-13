<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\NewsletterData;
use App\Models\Newsletter;

class CreateNewsletterAction
{
    public function execute(NewsletterData $newsletter, string $jobId): Newsletter
    {
        return Newsletter::firstOrCreate(
            ['uid' => $newsletter->uid],
            [
                'job_id' => $jobId,
                'subject' => $newsletter->subject,
                'from' => $newsletter->from,
                'date' => $newsletter->date,
                'content' => $newsletter->content,
                'summary' => $newsletter->summary,
            ]
        );
    }
}
