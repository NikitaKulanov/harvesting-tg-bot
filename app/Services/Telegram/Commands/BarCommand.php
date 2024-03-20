<?php

namespace App\Services\Telegram\Commands;

use App\Models\User;
use App\Services\Telegram\Bot;
use App\Services\Telegram\Commands\Basic\InactionCommand;
use App\Services\Telegram\DTO\Chat;
use App\Services\Telegram\Payloads\EditMessagePayload;
use App\Services\Telegram\Payloads\Keyboards\Buttons\InlineButton;
use App\Services\Telegram\Payloads\Keyboards\InlineKeyboard;
use App\Services\Telegram\Payloads\MessagePayload;

class BarCommand extends Command
{
    /**
     * Command for call
     */
    const CALL_TITLE_NEXT = '/barNext';
    const CALL_TITLE_BACK = '/barBack';

    /**
     * @param Bot $bot
     * @param Chat $chat
     * @param User $user
     * @return void
     */
    public function execute(Bot $bot, Chat $chat, User $user): void
    {
        $command = $bot->getCommandToExecute();
        $bar = $user->getFromStorage(self::nameToCall(), ['bar_level' => 1]);

        $data = ['1', '2', '3', '4', '5', '6', '7', '8',];

        $length = 3;

        $barLevel = $bar['bar_level'];

        $barLevel += ($command === self::CALL_TITLE_NEXT) ? 1 : (($command === self::CALL_TITLE_BACK) ? -1 : 0);

        if ($command == self::nameToCall()) {
            $result = $bot->sendMessage(
                MessagePayload::create($chat->id, 'Наш БАР')
                    ->setKeyboard(InlineKeyboard::create()->setKeyboardButton(
                        $this->getKeyboardButton($data, $length, $barLevel)
                    ))
            );
        } elseif ($command == self::CALL_TITLE_NEXT or $command == self::CALL_TITLE_BACK) {
            $result = $bot->editMessageText(
                EditMessagePayload::create($chat->id, $bar['message_id'], 'Наш БАР')
                    ->setKeyboard(InlineKeyboard::create()->setKeyboardButton(
                        $this->getKeyboardButton($data, $length, $barLevel)
                    ))
            );
        } else {
            return;
        }

        $user->saveToStorage([
            self::nameToCall() => ['message_id' => $result['message_id'] ?? null, 'bar_level' => $barLevel]
        ]);
    }

    private function getKeyboardButton(array $data, int $length, int $barLevel): array
    {
        $navigationButtons = [];
        $keyboardButton = [];

        foreach (array_slice($data, $length * ($barLevel - 1), $length) as $item) {
            $keyboardButton[] = [
                InlineButton::create()
                    ->setText($item)
                    ->setCallbackData(InactionCommand::nameToCall())
            ];
        }

        $navigationButtons[] = InlineButton::create()
            ->setText($barLevel)
            ->setCallbackData(InactionCommand::nameToCall());

        if ($barLevel * $length < count($data)) {
            $navigationButtons[] = InlineButton::create()
                ->setText('>>')
                ->setCallbackData(self::CALL_TITLE_NEXT);
        }

        if ($barLevel * $length > $length) {
            array_unshift($navigationButtons,
                InlineButton::create()
                    ->setText('<<')
                    ->setCallbackData(self::CALL_TITLE_BACK)
            );
        }

        $keyboardButton[] = $navigationButtons;

        return $keyboardButton;
    }

    /**
     * @return string
     */
    static function nameToCall(): string
    {
        return '/bar';
    }

    /**
     * @return string|null
     */
    public static function getPastCommand(): ?string
    {
        return null;
    }
}
