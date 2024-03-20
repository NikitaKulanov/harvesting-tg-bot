<?php

use App\Services\Telegram\Commands\BarCommand;
use App\Services\Telegram\Commands\Basic\BackCommand;
use App\Services\Telegram\Commands\Basic\BeginStartCommand;
use App\Services\Telegram\Commands\Basic\ConfirmSubscriptionCommand;
use App\Services\Telegram\Commands\Basic\ErrorCommand;
use App\Services\Telegram\Commands\Basic\InactionCommand;
use App\Services\Telegram\Commands\Basic\SubscribeCommand;
use App\Services\Telegram\Commands\Basic\UnknownCommand;
use App\Services\Telegram\Commands\Basic\UserAnswerCommand;
use App\Services\Telegram\Commands\StartCommand;
use App\Services\Telegram\Commands\TestCommand;

return [
    'settings' => [
        'token' => env('TG_BOT_TOKEN'),
        'url_webhook' => env('URL_SET_WEBHOOK'),

        /** Количество действий, если больше просить подписаться */
        'count_of_actions' => 99,

        /** Отвечать в группах */
        'answer_in_groups' => false,

        /** ID чата для отправки ошибок, если false ошибки отправляться не будут */
        'chat_id_for_errors' => env('CHAT_ID_FOR_ERRORS', false),

        /** Сообщение пользователю об ошибке */
        'error_message_to_the_user' => 'Произошла ошибка, приносим свои извинения, уже исправляем!',

        /** Сообщение пользователю об неизвестной команде */
        'message_unknown_command' => 'Простите, я вас не понял. Если хотите, можете начать со /start',

        /** Сообщение пользователю об выключенном боте */
        'message_shutdown_bot' => 'Простите, бот пока не доступен, обратитесь попозже',
    ],
    'simple_commands' => [
        StartCommand::nameToCall() => StartCommand::class,
        TestCommand::nameToCall() => TestCommand::class,
        BarCommand::nameToCall() => BarCommand::class,
        BarCommand::CALL_TITLE_NEXT => BarCommand::class,
        BarCommand::CALL_TITLE_BACK => BarCommand::class,
    ],
    'basic_commands' => [
        BackCommand::nameToCall() => BackCommand::class,
        ConfirmSubscriptionCommand::nameToCall() => ConfirmSubscriptionCommand::class,
        UnknownCommand::nameToCall() => UnknownCommand::class,
        ErrorCommand::nameToCall() => ErrorCommand::class,
        BeginStartCommand::nameToCall() => BeginStartCommand::class,
        SubscribeCommand::nameToCall() => SubscribeCommand::class,
        UserAnswerCommand::nameToCall() => UserAnswerCommand::class,
        InactionCommand::nameToCall() => InactionCommand::class,
    ],
    /** Commands requiring subscription */
    'subscription_required' =>  [
//        TestCommand::nameToCall()
    ],
    'channel_subscriptions' =>  [
        [
            'id' => '@NewAllNewsE',
            'title' => 'Новости',
            'url' => 'https://t.me/NewAllNewsE',
        ],
    ]
];
