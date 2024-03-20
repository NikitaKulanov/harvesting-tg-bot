<?php

namespace App\Contracts\Telegram;

use App\Services\Telegram\Payloads\InputFiles\InputFile;

interface InputFilePayload
{
    /**
     * @return InputFile[]
     */
    public function getContentForAttach(): array;
}
