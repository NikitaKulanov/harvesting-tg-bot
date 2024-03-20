<?php

namespace App\Http\Controllers;

use App\Services\Telegram\Bot;
use App\Services\Telegram\Commands\Basic\UnknownCommand;
use App\Services\Telegram\RequestClient;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TGController extends Controller
{
    public function setWebhookBot(RequestClient $TGClient, string $action): Response
    {
        $action === 'activation' ? $path = '/api/bot' : ($action === 'shutdown' ? $path = '/api/shutdownBot' : throw new \RuntimeException('Неправильное действие'));
        $response = $TGClient->setWebhook(
            config('bot.settings.url_webhook') . $path
        );

        return new Response(
            array_key_exists('description', $response->json()) ?
                $response['description'] : 'Successful!',
            $response->status()
        );
    }

    public function shutdownBot(Bot $bot): Response
    {
        $bot->sendMessageText(
            $bot->getUpdate()->getChat()->id,
            config('bot.message_shutdown_bot', 'Простите, бот пока не доступен, обратитесь попозже')
        );

        return new Response('', 204);
    }

    public function messageBot(Bot $bot): Response
    {
//                return new Response('', 200);

        $bot->executeCommand($bot->getUpdate()->getPayload());

        return new Response('', 204);
    }
}
