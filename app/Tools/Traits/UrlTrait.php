<?php

namespace App\Tools\Traits;

trait UrlTrait {

    /**
     * Add parameter to URL
     * @param string $url
     * @param string $key
     * @param string $value
     * @return string result URL
     */
    public static function setUrlParam($url, $key, $value = null)
    {
        $query = parse_url($url, PHP_URL_QUERY);
        if (!empty($query))
        {
            parse_str($query, $queryParams);
            $queryParams[$key] = $value;
            $url = str_replace("?$query", '?' . http_build_query($queryParams), $url);
        } else
        {
            $url .= '?' . urlencode($key) . '=' . urlencode($value);
        }
        return $url;
    } 

}
