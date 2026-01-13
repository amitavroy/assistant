<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

class NewsletterData extends Data
{
    public function __construct(
        public string $uid,
        public string $subject,
        public string $from,
        public string $date,
        public string $content,
        public ?string $summary = null,
    ) {}
}
