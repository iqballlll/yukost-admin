<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class TelegramHelper
{
    protected static $botToken;
    protected static $chatId;

    public static function sendMessage(string $message): bool
    {
        self::$botToken = env('TELEGRAM_BOT_TOKEN');
        self::$chatId = env('TELEGRAM_CHAT_ID');

        if (!self::$botToken || !self::$chatId) {
            // Token/chat_id belum diset di .env
            return false;
        }

        $url = "https://api.telegram.org/bot" . self::$botToken . "/sendMessage";

        $response = Http::post($url, [
            'chat_id' => self::$chatId,
            'text' => $message,
            'parse_mode' => 'Markdown',
        ]);

        return $response->successful();
    }
}
