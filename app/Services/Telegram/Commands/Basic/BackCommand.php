<?php

namespace App\Services\Telegram\Commands\Basic;

use App\Models\User;
use App\Services\Telegram\Bot;
use App\Services\Telegram\Commands\Command;
use App\Services\Telegram\DTO\Chat;
use Illuminate\Support\Facades\Log;

class BackCommand extends Command
{
    /**
     * @param Bot $bot
     * @param Chat $chat
     * @param User $user
     * @return void
     */
    public function execute(Bot $bot, Chat $chat, User $user): void
    {
        $bot->executeCommand($user->getFromStorage('past_command', BeginStartCommand::nameToCall()));
    }

    /**
     * @return string
     */
    static function nameToCall(): string
    {
        return '/back';
    }

    /**
     * @return string|null
     */
    public static function getPastCommand(): ?string
    {
        return null;
    }
}
