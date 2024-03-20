<?php

namespace App\Services\Telegram\Payloads;

use App\Contracts\Telegram\InputFilePayload;
use App\Services\Telegram\Payloads\InputFiles\InputPhoto;
use App\Services\Telegram\RequestClient;

class PhotoPayload extends Payload implements InputFilePayload
{
    private InputPhoto $inputPhoto;
    private string $caption;

    const METHOD_API = RequestClient::SEND_PHOTO;

    public function __construct(int $chatId, InputPhoto $inputPhoto)
    {
        $this->inputPhoto = $inputPhoto;
        $this->chatId = $chatId;
    }

    /**
     * @param int $chatId
     * @param InputPhoto $inputPhoto
     * @return PhotoPayload
     */
    public static function create(int $chatId, InputPhoto $inputPhoto): PhotoPayload
    {
        return new self($chatId, $inputPhoto);
    }

    /**
     * @param string $caption
     * @return PhotoPayload
     */
    public function setCaption(string $caption): PhotoPayload
    {
        $this->caption = $caption;
        return $this;
    }

    /**
     * @return array
     */
    public function getArrayForRequest(): array
    {
        $array = [
            'chat_id' => $this->chatId,
            'parse_mode' => $this->parseMode,
            'reply_markup' => json_encode($this->keyboard)
        ];

        if ($caption = $this->inputPhoto->getCaption()) {
            $array['caption'] = $caption;
        }

        if (isset($this->caption)) $array['caption'] = $this->caption;

        return $array;
    }

    /**
     * @return array
     */
    public function getContentForAttach(): array
    {
        return [$this->inputPhoto->toArrayForAttach()];
    }
}
