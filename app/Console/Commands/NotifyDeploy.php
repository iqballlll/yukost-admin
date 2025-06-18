<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\TelegramHelper;

class NotifyDeploy extends Command
{
    protected $signature = 'notify:deploy {--branch=main}';

    protected $description = 'Send Telegram notification for successful deployment';

    public function handle()
    {
        $branch = $this->option('branch') ?? 'main';
        $message = "✅ *Deploy Laravel Project berhasil!*\n"
            . "Branch: `{$branch}`\n"
            . "Timestamp: " . now()->toDateTimeString();

        $sent = TelegramHelper::sendMessage($message);

        if ($sent) {
            $this->info('Telegram notification sent.');
        } else {
            $this->error('Failed to send Telegram notification. Check your config.');
        }

        return 0;
    }
}
