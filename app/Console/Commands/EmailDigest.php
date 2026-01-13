<?php

namespace App\Console\Commands;

use App\Jobs\EmailDigestWorkflowJob;
use Illuminate\Console\Command;

class EmailDigest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:email-digest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate an email digest with your latest Newsletters';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        EmailDigestWorkflowJob::dispatchSync();
    }
}
