<?php

namespace App\Services\Telegram;

use App\Contracts\Telegram\InputFilePayload;
use App\Exceptions\TGApiException;
use App\Services\Telegram\Payloads\Payload;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class RequestClient
{
    const SET_WEBHOOK = 'setWebhook';
    const SEND_MESSAGE = 'sendMessage';
    const SEND_PHOTO = 'sendPhoto';
    const SEND_VIDEO = 'sendVideo';
    const SEND_MEDIA_GROUP = 'sendMediaGroup';
    const EDIT_MESSAGE_TEXT = 'editMessageText';
    const EDIT_MESSAGE_MEDIA = 'editMessageMedia';
    const GET_CHAT_MEMBER = 'getChatMember';

    private readonly string $urlBot;

    public function __construct(
        public Http $httpClient,
        string      $token
    )
    {
        $this->urlBot = 'https://api.telegram.org/bot' . $token;
    }

    /**
     * @throws TGApiException
     */
    private function checkException(Response $response, array $payload, string $methodAPI): void
    {
        if ($response->serverError() or $response->failed()) {
            $exception = new TGApiException('Incorrect call to TG API');
            $exception->chatIdUser = $payload['chat_id'];
            $exception->methodAPI = $methodAPI;
            $exception->responseJson = $response->body();
            $exception->requestJson = json_encode($payload, JSON_UNESCAPED_UNICODE);
            throw $exception;
        }
    }

    public function sendMessageText(int $chatId, string $text): Response
    {
        return $this->httpClient::post(
            $this->urlBot . '/' . self::SEND_MESSAGE,
            [
                'chat_id' => $chatId,
                'text' => $text,
            ],
        );
    }

    /**
     * @throws TGApiException
     */
    public function sendPayload(Payload $payload): Response
    {
        if ($payload instanceof InputFilePayload and $payload->hasFile()) {
            $response = Http::attach($payload->getContentForAttach())
                ->post(
                    $this->urlBot . '/' . $payload::METHOD_API,
                    $payload->getArrayForRequest()
                );
        } else {
            $response = Http::post(
                $this->urlBot . '/' . $payload::METHOD_API,
                $payload->getArrayForRequest()
            );
        }

        $this->checkException(
            $response,
            $payload->getArrayForRequest(),
            $payload::METHOD_API
        );

        return $response;
    }

    /**
     * @throws TGApiException
     */
    public function setWebhook(string $url): Response
    {
        $response = $this->httpClient::get(
            $this->urlBot . '/' . self::SET_WEBHOOK,
            ['url' => $url]
        );

        $this->checkException(
            $response,
            ['url' => $url],
            'setWebhook'
        );

        return $response;
    }

    /**
     * @throws TGApiException
     */
    public function checkChannelSubscription(string $channelId, int $userId): Response
    {
        $response = $this->httpClient::post(
            $this->urlBot . '/' . self::GET_CHAT_MEMBER,
            [
                'chat_id' => $channelId,
                'user_id' => $userId,
            ]
        );

        $this->checkException(
            $response,
            ['chat_id' => $channelId, 'user_id' => $userId,],
            self::GET_CHAT_MEMBER
        );

        return $response;
    }


}
