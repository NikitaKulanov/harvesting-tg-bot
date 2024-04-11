<?php

namespace App\Services\Telegram;

use App\Exceptions\TGApiException;
use App\Models\User;
use App\Services\Telegram\Commands\Basic\SubscribeCommand;
use App\Services\Telegram\Commands\Basic\UnknownCommand;
use App\Services\Telegram\Commands\Basic\UserAnswerCommand;
use App\Services\Telegram\Commands\Command;
use App\Services\Telegram\DTO\Builder\DTOUpdateBuilder;
use App\Services\Telegram\DTO\Sender;
use App\Services\Telegram\DTO\Update;
use App\Services\Telegram\Payloads\EditMessageMediaPayload;
use App\Services\Telegram\Payloads\EditMessagePayload;
use App\Services\Telegram\Payloads\MediaGroupPayload;
use App\Services\Telegram\Payloads\MessagePayload;
use App\Services\Telegram\Payloads\PhotoPayload;
use App\Services\Telegram\Payloads\VideoPayload;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Bot
{
    /**
     * Не учитывать, не сохранять
     * @var array
     */
    public readonly array $basicCommands;

    /**
     * Для выполнения этих команд требуется подписка
     * @var array|null
     */
    public readonly ?array $subscriptionRequired;

    /**
     * @var string Команда поставленная на выполнение
     */
    private string $commandToExecute;

    /**
     * @var Update
     */
    private Update $update;

    /**
     * @var User
     */
    private User $user;

    /**
     * @var array
     */
    private array $commands;

    /**
     * Количество действий, если больше просить подписаться
     *
     * @var int|null
     */
    private ?int $freeCountActions;


    public function __construct(
        public               readonly array $config,
        public RequestClient $requestClient,
        public Request       $request
    )
    {
        $requestArray = $request->json()->all();
        if (isset($requestArray['update_id'])) {
            $this->setUpdate(DTOUpdateBuilder::buildUpdateDTO($requestArray));
            $this->createUserFromSender($this->update->getSender());
            $this->freeCountActions = $config['settings']['count_of_actions'] ?? null;
            $this->commands = array_merge($config['simple_commands'], $config['basic_commands']);
            $this->basicCommands = $config['basic_commands'];
            $this->subscriptionRequired = $config['subscription_required'] ?? null;
        }
    }

    /**
     * @return Update
     */
    public function getUpdate(): Update
    {
        return $this->update;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param Update $update
     * @return void
     */
    public function setUpdate(Update $update): void
    {
        $this->update = $update;
    }

    /**
     * @return string
     */
    public function getCommandToExecute(): string
    {
        return $this->commandToExecute;
    }

    /**
     * @param Sender $sender
     * @return void
     */
    public function createUserFromSender(Sender $sender): void
    {
        if (!($user = User::where('id_tg', $sender->id)->first())) {
            $user = User::createFromSender($sender);
        }
        $this->user = $user;
    }

    /**
     * Попросить ли подписаться
     *
     * @param string $command
     * @return bool
     */
    private function askSubscription(string $command): bool
    {
        if (is_array($this->subscriptionRequired)) {
            if (in_array($command, $this->subscriptionRequired)) {
                if (!$this->checkChannelSubscriptions($this->user)) {
                    $this->executeBasicCommand(SubscribeCommand::nameToCall());
                    return true;
                }
            }
        } elseif (is_int($this->freeCountActions)) {
            if ($this->user->getCountActions() >= $this->freeCountActions) {
                if (!$this->checkChannelSubscriptions($this->user)) {
                    $this->executeBasicCommand(SubscribeCommand::nameToCall());
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param string $command
     * @param string $default
     * @return void
     */
    public function executeCommand(string $command, string $default = UnknownCommand::NAME_TO_CALL): void
    {
        if (!key_exists($command, $this->commands)) $command = $default;

        if ($this->user->botWaitAnswer()) $command = UserAnswerCommand::nameToCall();

        if (array_key_exists($command, $this->basicCommands)) {
            $this->executeBasicCommand($command, $default);
            return;
        }

        if ($this->askSubscription($command)) {
            $this->user->saveToStorage([
                'desired_command' => $command,
            ]);
            return;
        }

        $this->commandToExecute = $command;

        $commandToExecute = $this->getCommand($command);

        $commandToExecute->execute(
            $this,
            $this->update->getChat(),
            $this->user
        );

        $this->user->saveToStorage([
            'past_command' =>
                $commandToExecute::getPastCommand() ?? $this->user->getFromStorage('completed_command'),
            'completed_command' => $command,
            'count_of_actions' => $this->user->getCountActions() + 1,
            'desired_command' => null
        ]);

        $this->freeCountActions = $this->freeCountActions + 1;
    }

    /**
     * @param string $command
     * @param string $default
     * @return void
     */
    public function executeBasicCommand(string $command, string $default = UnknownCommand::NAME_TO_CALL): void
    {
        if (!key_exists($command, $this->commands)) $command = $default;

        if (!array_key_exists($command, $this->basicCommands)) {
            throw new \RuntimeException("You are trying to execute a simple command {$command} as a basic one");
        }

        $this->commandToExecute = $command;

        $this->getCommand($command)->execute(
            $this,
            $this->update->getChat(),
            $this->user
        );
    }

    public function searchCommand(string $text): ?Command
    {
        if (array_key_exists($text, $this->commands)) {
            return $this->getCommand($text);
        } else return null;
    }

    private function getCommand(string $command): Command
    {
        if (class_exists($class = $this->commands[$command])) {
            return new $class;
        } else throw new \RuntimeException("Class {$class} not found");
    }

    /**
     * Проверка подписки на канал
     *
     * @param User $user
     * @return bool
     * @throws TGApiException
     */
    public function checkChannelSubscriptions(User $user): bool
    {
        if (array_key_exists('channel_subscriptions', $this->config)) {
            foreach ($this->config['channel_subscriptions'] as $channel) {
                if ($this->requestClient->checkChannelSubscription(
                        $channel['id'],
                        $user->id_tg
                    )->json()['result']['status'] === 'left'
                ) {
                    return false;
                }
            }
        }
        return true;
    }

    public function sendMessageText(int $chatId, string $text): array
    {
        return $this->requestClient->sendMessageText($chatId, $text)->json()['result'];
    }

    public function sendMessage(MessagePayload $payload): array
    {
        return $this->requestClient->sendPayload($payload)->json()['result'];
    }

    public function sendPhoto(PhotoPayload $payload): array
    {
        return $this->requestClient->sendPayload($payload)->json()['result'];
    }

    public function sendVideo(VideoPayload $payload): array
    {
        return $this->requestClient->sendPayload($payload)->json()['result'];
    }

    public function sendMediaGroup(MediaGroupPayload $payload): array
    {
        return $this->requestClient->sendPayload($payload)->json()['result'];
    }

    public function editMessageText(EditMessagePayload $payload): array
    {
        return $this->requestClient->sendPayload($payload)->json()['result'];
    }

    public function editMessageMedia(EditMessageMediaPayload $payload): array
    {
        return $this->requestClient->sendPayload($payload)->json()['result'];
    }
}
