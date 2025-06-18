<?php

use Symfony\Component\Process\Process;
use Illuminate\Http\Request;

Route::post('/deploy-webhook', function (Request $request) {
    $deployScript = base_path('deploy.sh');

    $process = new Process([$deployScript]);
    $process->setEnv([
        'TELEGRAM_BOT_TOKEN' => env('TELEGRAM_BOT_TOKEN'),
        'TELEGRAM_CHAT_ID' => env('TELEGRAM_CHAT_ID'),
    ]);
    $process->run();

    if (!$process->isSuccessful()) {
        // Ambil output error untuk debug
        $errorOutput = $process->getErrorOutput();
        $output = $process->getOutput();

        // Log error ke laravel.log supaya bisa dicek
        \Log::error("Deploy script gagal: " . $errorOutput);
        \Log::info("Deploy script output: " . $output);

        return response("Deploy gagal: " . $errorOutput, 500);
    }

    return response('Deploy berhasil', 200);
});

Route::post('/deploy-manual', function (Request $request) {
    $message = $request->input('message.text');
    $chatId = $request->input('message.chat.id');

    if ($message === '/redeploy' && $chatId == env('TELEGRAM_CHAT_ID')) {
        $deployScript = base_path('deploy.sh');

        $process = new Process([$deployScript]);
        $process->setEnv([
            'TELEGRAM_BOT_TOKEN' => env('TELEGRAM_BOT_TOKEN'),
            'TELEGRAM_CHAT_ID' => env('TELEGRAM_CHAT_ID'),
        ]);
        $process->run();

        $text = $process->isSuccessful()
            ? '✅ Redeploy berhasil.'
            : "❌ Redeploy gagal:\n" . $process->getErrorOutput();

        // Kirim hasilnya ke Telegram
        Http::post("https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN') . "/sendMessage", [
            'chat_id' => env('TELEGRAM_CHAT_ID'),
            'text' => $text,
        ]);

        return response('OK', 200);
    }

    return response('Ignored', 200);
});
