<?php

namespace App\Services\Telegram\Payloads;

use App\Contracts\Telegram\InputFilePayload;
use App\Services\Telegram\Payloads\InputFiles\InputFile;
use App\Services\Telegram\Payloads\InputFiles\InputVideo;
use App\Services\Telegram\RequestClient;

class VideoPayload extends Payload implements InputFilePayload
{
    private InputVideo $inputVideo;
    private string $caption;

    const METHOD_API = RequestClient::SEND_VIDEO;

    public function __construct(int $chatId, InputVideo $inputVideo)
    {
        $this->chatId = $chatId;
        $this->inputVideo = $inputVideo;
    }

    /**
     * @param int $chatId
     * @param InputVideo $inputVideo
     * @return VideoPayload
     */
    public static function create(int $chatId, InputVideo $inputVideo): VideoPayload
    {
        return new self($chatId, $inputVideo);
    }

    /**
     * @param string $caption
     * @return VideoPayload
     */
    public function setCaption(string $caption): VideoPayload
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
            'thumbnail' => 'attach://' . $this->inputVideo->getThumbnail()->getFilename(),
            'chat_id' => $this->chatId,
            'parse_mode' => $this->parseMode,
            'reply_markup' => json_encode($this->keyboard)
        ];

        if ($caption = $this->inputVideo->getCaption()) {
            $array['caption'] = $caption;
        }

        if (isset($this->caption)) $array['caption'] = $this->caption;

        return $array;
    }

    /**
     * @return InputFile[]
     */
    public function getContentForAttach(): array
    {
        $array = [];
        $array[] = $this->inputVideo->toArrayForAttach();
        $this->inputVideo->getThumbnail()->setTitle($this->inputVideo->getThumbnail()->getFilename());
        $array[] = $this->inputVideo->getThumbnail()->toArrayForAttach();
        return $array;
    }
}
