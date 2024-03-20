<?php

namespace App\Exceptions;

use App\Services\Telegram\RequestClient;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class TGApiException extends Exception
{
    /**
     * ID чата, где произошла ошибка
     *
     * @var int
     */
    public int $chatIdUser;

    /**
     * Ответ TG
     *
     * @var string
     */
    public string $responseJson;

    /**
     * Payload отправленный в TG API
     *
     * @var string
     */
    public string $requestJson;

    /**
     * Метод отправленный в TG API
     *
     * @var string
     */
    public string $methodAPI;

    /**
     * Report or log an exception.
     *
     * @return void
     */
    public function report(RequestClient $requestClient)
    {
        /**
         * Отправка сообщения в чат, где произошла ошибка
         */
        if (isset($this->chatIdUser)) {
            $requestClient->sendMessageText(
                $this->chatIdUser,
                config(
                    'bot.settings.error_message_to_the_user',
                    'Произошла ошибка, приносим свои извинения, уже исправляем!'
                )
            );
        }

        /**
         * Отправка сообщения в группу с ошибками
         */
        if ($chatIdErrors = config('bot.settings.chat_id_for_errors')) {
            $requestClient->sendMessageText(
                $chatIdErrors,
                'Произошла ошибка при обращении к api TG! Подробнее в файле логов.'
            );
        }

        /**
         * Отправка сообщения в log файл
         */
        $log = $this->getMessage() . PHP_EOL;
        if(isset($this->methodAPI)) $log = $log . 'Method API: ' . $this->methodAPI . PHP_EOL;
        if(isset($this->responseJson)) $log = $log . 'Response: ' . $this->responseJson . PHP_EOL;
        if(isset($this->requestJson)) $log = $log . 'Request: ' . $this->requestJson . PHP_EOL;

        Log::channel('file_bot')->error(
            $log . 'Файл: ' . $this->getFile() . ' Строка: ' . $this->getLine() . ' Код: ' . $this->getCode() . PHP_EOL
        );
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function render(Request $request): Response
    {
        if ($request->path() === 'api/bot') {
            return response('', 204);
        } else return response('Произошла ошибка при обращении к api TG! Подробнее в файле логов.', 500);
    }
}
