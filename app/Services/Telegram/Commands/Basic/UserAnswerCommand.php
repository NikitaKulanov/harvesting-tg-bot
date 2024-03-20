<?php

namespace App\Services\Telegram\Commands\Basic;

use App\Models\User;
use App\Services\Telegram\Bot;
use App\Services\Telegram\Commands\Command;
use App\Services\Telegram\DTO\Chat;
use App\Services\Telegram\Payloads\MessagePayload;

class UserAnswerCommand extends Command
{
    /**
     * @param Bot $bot
     * @param Chat $chat
     * @param User $user
     * @return void
     */
    public function execute(Bot $bot, Chat $chat, User $user): void
    {
        $bot->sendMessage(
            MessagePayload::create($chat->id, $bot->getUpdate()->getPayload())
        );
        $user->setWaitingBotAnswer(false);
    }

    /**
     * @return string
     */
    static function nameToCall(): string
    {
        return '/userAnswer';
    }

    /**
     * @return string|null
     */
    public static function getPastCommand(): ?string
    {
        return null;
    }
}
