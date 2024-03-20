<?php

use App\Models\User;
use App\Services\Telegram\Payloads\Keyboards\Buttons\InlineButton;
use App\Services\Telegram\Payloads\Keyboards\Buttons\ReplyButton;
use App\Services\Telegram\Payloads\Keyboards\InlineKeyboard;
use App\Services\Telegram\Payloads\Keyboards\ReplyKeyboard;
use App\Services\Telegram\Payloads\MessagePayload;
use App\Services\Telegram\Payloads\PhotoPayload;
use Illuminate\Http\File;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return new Response(' Здесь живёт бот');
});

Route::get('/qwe', function (\Illuminate\Http\Request $request,\App\Services\Telegram\Bot $bot) {
    $arr = [1,2,3,4,5,6,7,8,9,10];
    foreach (array_slice($arr, 3, 5) as $var){
        echo $var;
    }
//    if (30 > false) {
//        echo 'qwe';
//    } else {
//        echo 'zxc';
//    }

//    $file = Storage::get('img/screenshot_chatTE.png');
//    dd($file);
//    return response($file)->header('Content-Type', 'image/png');
//    return Response::file($file);
//    dd($file);
//    echo 123;
});

Route::get('/test', function (\Illuminate\Http\Request $request,\App\Services\Telegram\Bot $bot) {
    dd(123);
    $x = secure_asset('storage/img/screenshot_chat.png');
    $x = Storage::exists('public/img/screenshot_chat.png');
    $x = PhotoPayload::create(123, 'img/screenshot_chat.png')
        ->setCaption('Описание фото');
//    $x = Storage::disk('local')->exists('screenshot_chat.png');
//    https://d7a4-146-120-78-123.ngrok-free.app/storage/screenshot_chat.png
//     $x = MessagePayload::create()
//        ->setChatId(123)
//        ->setText('Это ТЕСТ')
//        ->setKeyboard(
//            InlineKeyboard::create()->setKeyboardButton([
//                [
//                    InlineKeyboardButton::create()->setText('qwe')->setCallbackData('qwe'),
//                ]
//            ])
//        );
    dd($x);
//    throw new \App\Exceptions\TGApiException();
//    dd(config('bot.settings.chat_id_for_errors'));
//    $exception = new \App\Exceptions\TGApiException('zxc');
//    $exception->chatIdUser = 6312190432;
//    throw $exception;
//    $user = \App\Models\User::find(13);
//    $user->saveToStorage('qwe', 12345);
//    $user = User::build(1234567, 'qwert');
//        User::create([
//        'id_tg' => 123,
//        'first_name' => '$firstName',
//        'last_name' => '$lastName',
//        'user_name' => '$userName',
//        'language_code' => '$languageCode',
//        'storage' => '{}',
//    ]);
//    $user = new \App\Models\User();
//    $user->id_tg = 123456;
//    $user->first_name = 'qwer';
//    $user->user_name = 'asd';
//    $user->storage = ['qwe' => 123, 'zxc' => 'awde'];
//    $user->save();
//    $user = \App\Models\User::find(3);
//    $arr = json_decode($user->storage, true);
//    $arr['zxc'] = 148;
//    $user->storage = ['zxc' => 123];
//    $user->save();
    dd($user->getFromStorage('qwe'));
    return $user;
//    dd(MessagePayload::create()
//        ->setChatId(123)
//        ->setText('Это СТАРТ')
//        ->setReplyMarkup(
//            ReplyKeyboard::create()->setKeyboard([
//                [
//                    KeyboardButton::create()->setText('Текст для кнопки')
//                ]
//            ])->setResizeKeyboard(true)
//        )->toArray());
//    $text = '/start';
//    $botSetting = config('bot_setting');
//    if (array_key_exists($text, $botSetting)) {
//        $bot->executeCommand();
//    }
//
//    return view('welcome');
//    $classString = \App\Services\TG\Commands\StartCommand::class;
//    dd(new $classString);
//
//// Получение имени класса из строки
//    $className = str_replace('::class', '', $classString);
//
//// Проверка существования класса
//    if (class_exists($className)) {
//        // Создание объекта класса
//        $command = new $className();
//        dd($command);
//    } else {
//        dd("Класс $className не существует.");
//    }
});
