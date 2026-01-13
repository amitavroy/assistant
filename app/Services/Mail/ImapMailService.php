<?php

namespace App\Services\Mail;

use DirectoryTree\ImapEngine\Laravel\Facades\Imap;
use Illuminate\Support\Collection;

class ImapMailService implements MailServiceInterface
{
    public function getMessages(?string $folderName = null): Collection
    {
        $mailbox = Imap::mailbox('default');

        $folder = $mailbox->folders()->find($folderName ?? config('companion.default_folder'));

        if (! $folder) {
            return collect();
        }

        $messages = $folder->messages()
            ->withBody()
            ->withHeaders()
            ->get();

        return $messages->map(function ($message) {
            return [
                'uid' => $message->uid(),
                'subject' => $message->subject(),
                'from' => $message->from()?->email(),
                'date' => $message->date(),
                'content' => $message->text(),
            ];
        });
    }
}
