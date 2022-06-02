<?php
header("HTTP/1.1 200 OK");
require 'libs/simplevk-master/autoload.php';
require 'controllers/MainController.php';

use DigitalStar\vk_api\VK_api as vk_api; // Основной класс
use DigitalStar\vk_api\Execute;

const VK_KEY = "20f20fe68f96c9656990146b17e44463aa5e0a25fbf4a830dc91457934faed85068aaa56b5df0df7e9e0e";  // Токен сообщества
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

$botWork = new botExtension();

// Создает проверку на новое сообщение
if ($data->type == 'message_new' or $data->type == 'message_edit') {
    if (mb_strtolower($message) == '110 главная' || $message == 'Начать') {
        $vk->sendButton($peer_id, "Лови!!!🔽", [[$btn_1, $btn_2, $btn_3]]);
    } else if (mb_strtolower($message) == 'салам, бот' || mb_strtolower($message) == 'салам бот') {
        $vk->reply('Шалом');
    } else if (mb_strtolower($message) == 'расписание' || mb_strtolower($message) == 'расписание ебаное') {
        // получаем день
        $tags = $botWork->getDairy();

        $firstText = $tags['text'][$botWork->firstText];
        $secondText = $tags['text'][$botWork->secondText];

        $firstHref = $tags['href'][$botWork->firstHref];
        $secondHref = $tags['href'][$botWork->secondHref];

        if($secondText < $firstText) {
            $vk->reply("Расписание на $secondText: " . $secondHref);
            $vk->reply("Расписание на $firstText: " . $firstHref);
        } else {
            $vk->reply("Расписание на $firstText: " . $firstHref);
            $vk->reply("Расписание на $secondText: " . $secondHref);
        }
    } else if (mb_strtolower($message) == 'команды') {
        $vk->reply($botWork->commands());
    } else if (mb_strtolower($message) == 'ссылки') {

        $link = $botWork->getLinks();

        $vk->reply("Постоянные ссылки преподавателей для дистанционного обучения:\n $link");
    } else if (mb_strtolower($message) == 'книги') {

        $files = $botWork->getAllFilesFromDirectory('./books');

        foreach ($files as $file) {
            if ($file != '.' and $file != '..') {
                $vk->sendDocMessage($peer_id, "./books/${file}");
            }
        }
    } else if (mb_strtolower($message) == 'ответы') {
        $files = $botWork->getAllFilesFromDirectory('./answers');
        foreach ($files as $file) {
            if ($file != '.' and $file != '..') {
                $vk->sendDocMessage($peer_id, "./answers/${file}");
            }
        }
        $vk->reply("Ответы Голицинский: https://otvetkin.info/reshebniki/5-klass/angliyskiy-yazyk/golicynskij-7");
        $vk->reply("Решебник Абрамяна: https://uteacher.ru/reshebnik-abramyan/");
    } else if (mb_strtolower($message) == 'почты') {
        $mails = $botWork->getStringFromFiles('files/mails.txt');

        $vk->reply($mails);
    } else if (mb_strtolower($message) == 'обед') {
        $vk->sendImage($peer_id, 'files/Screenshot_3.png');
    } else if (mb_strtolower($message) == 'бот, скинь аниме' || mb_strtolower($message) == 'бот скинь аниме') {
        $animes = $botWork->getStringFromFiles('files/anime.txt');

        $vk->reply($animes[mt_rand(1, count($animes))]);
    }
}