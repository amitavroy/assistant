<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\NewsletterData;
use App\Models\Newsletter;

class CreateNewsletterAction
{
    public function execute(NewsletterData $newsletter): Newsletter
    {
        return Newsletter::firstOrCreate(
            ['uid' => $newsletter->uid],
            [
                'subject' => $newsletter->subject,
                'from' => $newsletter->from,
                'date' => $newsletter->date,
                'content' => $newsletter->content,
                'summary' => $newsletter->summary,
            ]
        );
    }
}
