<?php

namespace App\Services\Telegram\Commands;

use App\Models\User;
use App\Services\Telegram\Bot;
use App\Services\Telegram\DTO\Chat;
use App\Services\Telegram\Payloads\InputFiles\InputPhoto;
use App\Services\Telegram\Payloads\InputFiles\InputVideo;
use App\Services\Telegram\Payloads\Keyboards\Buttons\InlineButton;
use App\Services\Telegram\Payloads\Keyboards\InlineKeyboard;
use App\Services\Telegram\Payloads\MediaGroupPayload;
use App\Services\Telegram\Payloads\MessagePayload;
use App\Services\Telegram\Payloads\PhotoPayload;
use App\Services\Telegram\Payloads\VideoPayload;
use Illuminate\Support\Facades\Log;

class TestCommand extends Command
{
    /**
     * @param Bot $bot
     * @param Chat $chat
     * @param User $user
     * @return void
     */
    public function execute(Bot $bot, Chat $chat, User $user): void
    {
        $result = $bot->sendMessage(
            MessagePayload::create($chat->id, 'Test!')
        );

//        $user->setWaitingBotAnswer();

//
//        $bot->sendMessage(
//            MessagePayload::create()
//                ->setChatId($chat->id)
//                ->setText('Жду ответа')
//        );

        // sendPhoto

//        $bot->sendPhoto(
//            PhotoPayload::create($chat->id, InputPhoto::create('screenshot_chatT.png'))
//            ->setCaption('Описание фото')
//                ->setKeyboard(
//                    InlineKeyboard::create()->setKeyboardButton([
//                        [
//                            InlineButton::create()->setText('qwe')->setCallbackData('qwe'),
//                        ]
//                    ])
//                )
//        );

        // sendVideo

//        $bot->sendVideo(VideoPayload::create($chat->id, InputVideo::create('IMG_2576.MP4'))
//            ->setCaption('Описание видео')
//            ->setKeyboard(
//                InlineKeyboard::create()->setKeyboardButton([
//                    [
//                        InlineButton::create()->setText('qwe')->setCallbackData('qwe'),
//                    ]
//                ])
//            )
//        );

//        $bot->requestClient->sendMediaGroup(MediaGroupPayload::create($chat->id, [
//            InputVideo::create('IMG_2576.MP4')->setCaption('Описание'),
//            InputPhoto::create('screenshot_chatT.png')
//        ]));
    }

    /**
     * @return string
     */
    static function nameToCall(): string
    {
        return '/test';
    }

    /**
     * @return string|null
     */
    public static function getPastCommand(): ?string
    {
        return null;
    }
}
