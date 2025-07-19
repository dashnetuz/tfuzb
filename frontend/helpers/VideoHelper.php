<?php

namespace frontend\helpers;

class VideoHelper
{
    /**
     * YouTube linkidan video ID ajratib oladi
     * @param string \$url
     * @return string|null
     */
    public static function getYoutubeId(string $url): ?string
    {
        preg_match('/(?:v=|\/)([0-9A-Za-z_-]{11}).*/', $url, $matches);
        return $matches[1] ?? null;
    }
}
