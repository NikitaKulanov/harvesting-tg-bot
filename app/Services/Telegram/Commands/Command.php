<?php

namespace App\Services\Telegram\Commands;

use App\Models\User;
use App\Services\Telegram\Bot;
use App\Services\Telegram\DTO\Chat;

abstract class Command
{
    /**
     * The Telegram command description.
     *
     * @var string
     */
    protected string $description;

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description ?? null;
    }

    /**
     * @return string|null
     */
    abstract static public function getPastCommand(): ?string;

    /**
     * Command handler
     *
     * @param Bot $bot
     * @param Chat $chat
     * @param User $user
     * @return void
     */
    abstract public function execute(Bot $bot, Chat $chat, User $user): void;

    /**
     * Command for call
     *
     * @return string
     */
    abstract static function nameToCall(): string;
}
