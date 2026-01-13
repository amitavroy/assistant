<?php

namespace App\Services\Mail;

use App\Data\NewsletterData;
use Illuminate\Support\Collection;

interface MailServiceInterface
{
    /**
     * @return Collection<int, NewsletterData>
     */
    public function getMessages(?string $folderName = null): Collection;
}
