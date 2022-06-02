<?php
header("HTTP/1.1 200 OK");
require 'libs/simplevk-master/autoload.php';

use DigitalStar\vk_api\VK_api as vk_api; // Основной класс
use DigitalStar\vk_api\Execute;

const VK_KEY = "ТОКЕН";  // Токен сообщества
const ACCESS_KEY = "КОД";  // Тот самый ключ из сообщества 
const VERSION = "5.126"; // Версия API VK

$vk = vk_api::create(VK_KEY, VERSION)->setConfirm(ACCESS_KEY);
// создание вида кнопок и задание им имени
$btn_1 = $vk->buttonText('Расписание', 'green', ['command' => 'btn_1']);
$btn_2 = $vk->buttonText('Ссылки', 'red', ['command' => 'btn_2']);
$btn_3 = $vk->buttonText('Команды', 'blue', ['command' => 'btn_3']);
$btn_4 = $vk->buttonText('Главная', 'blue', ['command' => 'btn_4']);

// ===================

// Инициализация переменных
$vk->initVars($peer_id, $message, $payload, $vk_id, $type, $data);
$vk = new Execute($vk);

// Создает проверку на новое сообщение
if ($data->type == 'message_new' or $data->type == 'message_edit') {
    if (mb_strtolower($message) == '110 главная' || $message == 'Начать') {
        $vk->sendButton($peer_id, "Лови!!!🔽", [[$btn_1, $btn_2, $btn_3]]);
    } else if (mb_strtolower($message) == 'салам, бот' || mb_strtolower($message) == 'салам бот') {
        $vk->reply('Шалом');
    } else if (mb_strtolower($message) == 'расписание' || mb_strtolower($message) == 'расписание ебаное') {
        require 'parser.php';

        // получаем день
        $dairy = new Parser();
        $tags = $dairy->getDairy();

        $firstText = $tags['text'][$dairy->firstText];
        $secondText = $tags['text'][$dairy->secondText];

        $firstHref = $tags['href'][$dairy->firstHref];
        $secondHref = $tags['href'][$dairy->secondHref];

        if($secondText < $firstText) {
            $vk->reply("Расписание на $secondText: " . $secondHref);
            $vk->reply("Расписание на $firstText: " . $firstHref);
        } else {
            $vk->reply("Расписание на $firstText: " . $firstHref);
            $vk->reply("Расписание на $secondText: " . $secondHref);
        }
    } else if (mb_strtolower($message) == 'команды') {
        $vk->reply("Мои команды:\n
            \t 1. Расписание
            \t 2. Ссылки
            \t 3. Книги
            \t 4. Ответы
            \t 5. Почты
            \t 6. Обед
        ");
    } else if (mb_strtolower($message) == 'ссылки') {
        require 'parser.php';

        $pars_link = new Parser();
        $link = $pars_link->getLinks();

        $vk->reply("Постоянные ссылки преподавателей для дистанционного обучения:\n $link");
    } else if (mb_strtolower($message) == 'книги') {
        $files = scandir('./books');
        foreach ($files as $file) {
            if ($file != '.' and $file != '..') {
                $vk->sendDocMessage($peer_id, "./books/${file}");
            }
        }
    } else if (mb_strtolower($message) == 'ответы') {
        $files = scandir('./answers');
        foreach ($files as $file) {
            if ($file != '.' and $file != '..') {
                $vk->sendDocMessage($peer_id, "./answers/${file}");
            }
        }
        $vk->reply("Ответы Голицинский: https://otvetkin.info/reshebniki/5-klass/angliyskiy-yazyk/golicynskij-7");
        $vk->reply("Решебник Абрамяна: https://uteacher.ru/reshebnik-abramyan/");
    } else if (mb_strtolower($message) == 'почты') {
        $file = fopen("files/mails.txt", "r");
        $mails = [];

        while (!feof($file)) {
            $mails[] = fgets($file);
        }
        fclose($file);

        $mails = implode('', $mails);
        $vk->reply($mails);
    } else if (mb_strtolower($message) == 'обед') {
        $vk->sendImage($peer_id, 'files/Screenshot_3.png');
    } else if (mb_strtolower($message) == 'бот, скинь аниме' || mb_strtolower($message) == 'бот скинь аниме') {
        $file = fopen("files/anime.txt", "r");
        $animes = [];

        while (!feof($file)) {
            $animes[] = fgets($file);
        }
        fclose($file);

        $vk->reply($animes[mt_rand(1, count($animes))]);
    }
}