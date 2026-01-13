<?php

namespace App\Services\Mail;

use Illuminate\Support\Collection;

interface MailServiceInterface
{
    public function getMessages(?string $folderName = null): Collection;
}
