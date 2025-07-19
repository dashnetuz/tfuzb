<?php

namespace frontend\helpers;

class StringHelper
{
    public static function slugify($text)
    {
        // Ruscha harflarni lotinga aylantirish
        $cyr = [
            'а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п',
            'р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я',
            'А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П',
            'Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я'
        ];
        $lat = [
            'a','b','v','g','d','e','yo','zh','z','i','y','k','l','m','n','o','p',
            'r','s','t','u','f','x','ts','ch','sh','sch','','y','','e','yu','ya',
            'A','B','V','G','D','E','Yo','Zh','Z','I','Y','K','L','M','N','O','P',
            'R','S','T','U','F','X','Ts','Ch','Sh','Sch','','Y','','E','Yu','Ya'
        ];
        $text = str_replace($cyr, $lat, $text);

        // Lotin harflari, sonlar va defisdan boshqa hamma belgilarni olib tashlaymiz
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
        $text = trim($text, '-');
        $text = strtolower($text);
        $text = preg_replace('~[^-\w]+~', '', $text);

        return $text ?: 'n-a';
    }
}
