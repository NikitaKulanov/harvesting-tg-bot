<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Services\Telegram\DTO\Sender;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_tg',
        'first_name',
        'last_name',
        'user_name',
        'language_code',
        'storage',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [

    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Save to json storage
     * Default cannot save an empty array
     *
     * @param array|string $value
     * @return void
     */
    public function setStorageAttribute(array|string $value): void
    {
        $this->attributes['storage'] = is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
    }

    /**
     * Get from json storage
     *
     * @param string $value
     * @return array
     */
    public function getStorageAttribute(string $value): array
    {
        return json_decode($value, true);
    }

    public function setWaitingBotAnswer(bool $value = true)
    {
        $this->saveToStorage([
            'wait_bot_answer' => $value
        ]);
    }

    public function botWaitAnswer(): bool
    {
        if ($value = $this->getFromStorage('wait_bot_answer')) {
            return $value;
        } else {
            $this->saveToStorage(['wait_bot_answer' => false]);
            return false;
        }
    }

    /**
     * Store in storage
     *
     * @param array $payload
     * @return $this
     */
    public function saveToStorage(array $payload): User
    {
        $this->storage = array_merge($this->storage, $payload);
        $this->save();
        return $this;
    }

    /**
     * Вернёт false если такого ключа нет в массиве
     *
     * @param array|string $payload
     * @param null $default
     * @return array|string|int|bool|null
     */
    public function getFromStorage(array|string $payload, $default = null): array|string|int|null|bool
    {
        if (is_string($payload)) {
            return $this->storage[$payload] ?? $default;
        } else {
            $array = [];
            foreach ($payload as $item) {
                if (array_key_exists($item, $this->storage)) {
                    $array[$item] = $this->storage[$item];
                }
            }
            return $array !== [] ? $array : $default;
        }
    }

    /**
     * Получить количество действий сделанных в боте
     *
     * @return int
     */
    public function getCountActions(): int
    {
        if ($count = $this->getFromStorage('count_of_actions')) {
            return $count;
        } else {
            $this->saveToStorage(['count_of_actions' => 0]);
            return 0;
        }
    }

    /**
     * @param User $user
     * @param string $key
     * @param array|string|int|null $value
     * @return User
     */
    public static function storeToStorage(
        User                  $user,
        string                $key,
        array|string|int|null $value = null
    ): User
    {
        $array = $user->storage;
        $array[$key] = $value;
        $user->storage = $array;
        $user->save();
        return $user;
    }

    public static function createFromSender(Sender $sender): User
    {
        return User::create([
            'id_tg' => $sender->id,
            'first_name' => $sender->firstName,
            'last_name' => $sender->lastName ?? null,
            'user_name' => $sender->userName ?? null,
            'language_code' => $sender->languageCode ?? null,
            'storage' => [],
        ]);
    }
}
