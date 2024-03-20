<?php

namespace App\Services\Telegram\Commands;

use App\Models\User;
use App\Services\Telegram\Bot;
use App\Services\Telegram\DTO\Chat;
use App\Services\Telegram\Payloads\Keyboards\Buttons\ReplyButton;
use App\Services\Telegram\Payloads\Keyboards\ReplyKeyboard;
use App\Services\Telegram\Payloads\MessagePayload;

class StartCommand extends Command
{
    protected string $description = 'This is the starting command';

    /**
     * @param Bot $bot
     * @param Chat $chat
     * @param User $user
     * @return void
     */
    public function execute(Bot $bot, Chat $chat, User $user): void
    {
        $bot->sendMessage(
            MessagePayload::create($chat->id, 'Это СТАРТ')
                ->setKeyboard(
                    ReplyKeyboard::create()->setKeyboardButton([
                        [
                            ReplyButton::create()->setText('Текст для кнопки')
                        ]
                    ])->setResizeKeyboard(true)
                )
        );
    }

    /**
     * @return string
     */
    static function nameToCall(): string
    {
        return '/start';
    }

    /**
     * @return string|null
     */
    public static function getPastCommand(): ?string
    {
        return null;
    }
}
