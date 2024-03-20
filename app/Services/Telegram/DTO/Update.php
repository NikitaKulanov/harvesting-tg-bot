<?php

namespace App\Services\Telegram\DTO;

class Update
{
    /**
     * @param int $updateId Уникальный идентификатор обновления
     * @param Message|null $message Новое входящее сообщение любого вида — text, photo, sticker, etc.
     * @param CallbackQuery|null $callbackQuery Новый входящий запрос обратного вызова
     */
    public function __construct(
        public readonly int $updateId,
        public readonly ?Message $message,
        public readonly ?CallbackQuery $callbackQuery,
    )
    {
    }

    public function getChat(): ?Chat
    {
        return $this->message->chat ?? $this->callbackQuery->message->chat ?? null;
    }

    public function getSender(): ?Sender
    {
        return $this->message->sender ?? $this->callbackQuery->sender ?? null;
    }

    public function getPayload(): ?string
    {
        return $this->message->text ?? $this->callbackQuery->data ?? null;
    }
}
