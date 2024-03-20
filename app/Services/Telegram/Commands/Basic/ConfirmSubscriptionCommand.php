<?php

namespace App\Services\Telegram\Commands\Basic;

use App\Exceptions\TGApiException;
use App\Models\User;
use App\Services\Telegram\Bot;
use App\Services\Telegram\Commands\Command;
use App\Services\Telegram\DTO\Chat;
use App\Services\Telegram\Payloads\MessagePayload;

class ConfirmSubscriptionCommand extends Command
{

    /**
     * @param Bot $bot
     * @param Chat $chat
     * @param User $user
     * @return void
     * @throws TGApiException
     */
    public function execute(Bot $bot, Chat $chat, User $user): void
    {
        if ($bot->checkChannelSubscriptions($user)) {
            $bot->sendMessage(
                MessagePayload::create($chat->id, 'Спасибо!')
            );
            // Выполнить желаемую команду, до просьбы подписаться
            $bot->executeCommand(
                $user->getFromStorage('desired_command', BeginStartCommand::nameToCall())
            );
        } else {
            $bot->sendMessage(
                MessagePayload::create($chat->id, 'Простите, но вы не подписались :(')
            );
            $bot->executeBasicCommand(SubscribeCommand::nameToCall());
        }
    }

    /**
     * @return string
     */
    static function nameToCall(): string
    {
        return '/confirmSubscription';
    }

    /**
     * @return string|null
     */
    static public function getPastCommand(): ?string
    {
        return null;
    }
}
