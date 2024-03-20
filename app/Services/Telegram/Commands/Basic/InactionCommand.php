<?php

namespace App\Services\Telegram\Commands\Basic;

use App\Models\User;
use App\Services\Telegram\Bot;
use App\Services\Telegram\Commands\Command;
use App\Services\Telegram\DTO\Chat;

class InactionCommand extends Command
{
    /**
     * @param Bot $bot
     * @param Chat $chat
     * @param User $user
     * @return void
     */
    public function execute(Bot $bot, Chat $chat, User $user): void
    {
        // Команда для бездействия
        return;
    }

    /**
     * @return string
     */
    static function nameToCall(): string
    {
        return '/inaction';
    }

    /**
     * @return string|null
     */
    public static function getPastCommand(): ?string
    {
        return null;
    }
}
