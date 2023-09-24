<?php

namespace App\Console\Commands\Telegram;

use App\Models\TgUser;
use App\Services\Telegram;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;

class GetUpdates extends Command
{
    protected $signature = 'telegram:get-updates';

    /**
     * @throws GuzzleException
     */
    public function handle(): bool
    {
        $telegram = new Telegram(config('connects.telegram.botToken'));
        $response = $telegram->getUpdates();
        if ($response->ok) {
            foreach ($response->result as $update) {
                $fromId = $update->message->from->id;
                $userId = base64_decode(str_replace('/start ', '', $update->message->text));
                $firstName = $update->message->from->first_name;
                $username = $update->message->from->username;

                if (is_numeric($userId)) {
                    $tgUser = TgUser::updateOrCreate([
                        'user_id' => is_numeric($userId) ? $userId : null,
                        'chat_id' => $fromId
                    ], [
                        'first_name' => $firstName,
                        'username' => $username
                    ]);

                    if ($tgUser->created_at >= Carbon::now()->subSeconds(30)) {
                        // TODO add text
                        $telegram->sendMessage($fromId, 'Your account has successful connected');
                    }
                }
            }
        }

        return true;
    }
}
