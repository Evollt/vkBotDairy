<?php
// импорт парсера
require 'libs/phpQuery/phpQuery-onefile.php';

class MainController {
    public $firstText = 0;
    public $secondText = 1;

    public $firstHref = 8;
    public $secondHref = 18;

    public function getDairy() {
        // получает контент со страницы
        $str = file_get_contents('https://www.mgkit.ru/studentu/raspisanie-zanatij');
        $pq = phpQuery::newDocument($str);
        $href = [];
        $text = [];

        // находит все теги a
        $links = $pq->find('a');

        foreach ($links as $link) {

            $pqLink = pq($link); //pq делает объект phpQuery

            // получает все тексты ссылки из страницы
            $textStart[] = $pqLink->html();
            $hrefStart[] = $pqLink->attr('href');
        }

        foreach($hrefStart as $htt) {
            $url_parse = parse_url($htt);

            if($url_parse['scheme'] == 'https' && $url_parse['host'] == 'drive.google.com') {
                array_push($href, $htt);
            }
        }

        for($i = 0; $i < count($textStart); $i++) {
            if($textStart[$i] == 'Изменения в расписании учебных занятий на ') {
                array_push($text, $textStart[$i + 1]);
            }
        }

        return [
            'text' => $text,
            'href' => $href,
        ];
    }

    public function getLinks() {
        $str = file_get_contents('https://www.mgkit.ru/studentu/raspisanie-zanatij');
        $pq = phpQuery::newDocument($str);
        $link = '';

        // находит все теги a
        $links = $pq->find('a');
        foreach ($links as $link) {

            $pqLink = pq($link); //pq делает объект phpQuery

            // получает все тексты ссылки из страницы
            $hrefStart[] = $pqLink->attr('href');
        }

        for($i = 0; $i < count($hrefStart); $i++) {
            // получаем ссылку, в которой будут равны спаршенные ссылки и вторая написанная
            if($hrefStart[$i] == 'https://docs.google.com/spreadsheets/d/1F7nprxnJRvl7cA-33-L9UmojJunsP7niDfwEep3_K0s/edit?usp=sharing') {
                $link = $hrefStart[$i];
            }
        }

        return $link;
    }

    public function commands() {
        // возвращаем текст команды
        return "Мои команды:\n
            \t 1. Расписание
            \t 2. Ссылки
            \t 3. Книги
            \t 4. Ответы
            \t 5. Почты
            \t 6. Обед
        ";
    }

    public function getAllFilesFromDirectory($dir) {
        // открываем директорию и добавляем в массив все имена файлов оттуда
        $files = scandir($dir);
        
        return $files;
    }

    public function getStringFromFiles($filename) {
        // открываем файл для чтения
        $file = fopen($filename, "r");
        $files = [];

        // проверка на то, чтобы не был конец файла
        while (!feof($file)) {
            // пока не достигнут конец файла мы читаем строчку)
            $files[] = fgets($file);
        }
        fclose($file);

        // это конкотенация всех элементов в массиве files, но дело в том, что я не хочу, чтобы массив anime конкотенировался(это еще один вызов этого метода)
        if($filename != 'files/anime.txt') {
            $files = implode('', $files);
        }

        return $files;
    }
}
