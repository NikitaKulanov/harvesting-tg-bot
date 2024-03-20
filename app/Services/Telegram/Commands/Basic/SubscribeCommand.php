<?php

namespace App\Services\Telegram\Commands\Basic;

use App\Models\User;
use App\Services\Telegram\Bot;
use App\Services\Telegram\Commands\Command;
use App\Services\Telegram\DTO\Chat;
use App\Services\Telegram\Payloads\Keyboards\Buttons\InlineButton;
use App\Services\Telegram\Payloads\Keyboards\InlineKeyboard;
use App\Services\Telegram\Payloads\MessagePayload;

class SubscribeCommand extends Command
{
    /**
     * @param Bot $bot
     * @param Chat $chat
     * @param User $user
     * @return void
     */
    public function execute(Bot $bot, Chat $chat, User $user): void
    {
        if (array_key_exists('channel_subscriptions', $bot->config)) {
            $keyboardButton = [];
            foreach ($bot->config['channel_subscriptions'] as $channel) {
                $keyboardButton[] = [
                    InlineButton::create()
                        ->setText('Подписаться на канал: ' . $channel['title'])
                        ->setCallbackData('подписаться')
                        ->setUrl($channel['url'])
                ];
            }
            $keyboardButton[] = [
                InlineButton::create()
                    ->setText('Я подписался')
                    ->setCallbackData(ConfirmSubscriptionCommand::nameToCall())
            ];
            $bot->sendMessage(
                MessagePayload::create($chat->id, 'Подпишись пожалуйста!')
                    ->setKeyboard(InlineKeyboard::create()->setKeyboardButton($keyboardButton))
            );
        } else {
            $bot->sendMessage(
                MessagePayload::create($chat->id, 'Вы уже на всё подписаны')
            );
        }
    }

    /**
     * @return string
     */
    static function nameToCall(): string
    {
        return '/subscribe';
    }

    /**
     * @return string|null
     */
    public static function getPastCommand(): ?string
    {
        return null;
    }
}
