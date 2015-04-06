<?php

namespace Franzip\SerpScraper\Helpers;

/**
 * Generate the correct URL to scrape for a given Search Engine.
 *
 * @package  SerpScraper
 */
class SerpUrlGenerator
{
    // search engines URLs params
    const GOOGLE_BASE_URL           = "http://www.google.com/search?";
    const GOOGLE_QUERY_SEPARATOR    = "&";
    const GOOGLE_SEARCH_PARAM       = "q=";
    const GOOGLE_OFFSET_PARAM       = "start=";
    const BING_BASE_URL             = "http://www.bing.com/search?";
    const BING_QUERY_SEPARATOR      = "&";
    const BING_SEARCH_PARAM         = "q=";
    const BING_OFFSET_PARAM         = "first=";
    const YAHOO_BASE_URL            = "https://search.yahoo.com/search?";
    const YAHOO_QUERY_SEPARATOR     = "&";
    const YAHOO_SEARCH_PARAM        = "p=";
    const YAHOO_OFFSET_PARAM        = "b=";
    const ASK_BASE_URL              = "http://us.ask.com/web?";
    const ASK_QUERY_SEPARATOR       = "&";
    const ASK_SEARCH_PARAM          = "q=";
    const ASK_OFFSET_PARAM          = "page=";

    /**
     * Generate a search engine URL to scrape.
     * Different engines yield a different URL structure.
     * The final result will be something like: "http://www.google.com/search?q=foo&start=10"
     * @param   string $engine
     * @param   string $keyword
     * @param   int    $offset
     * @return  string
     */
    public static function makeUrl($engine, $keyword, $offset)
    {
        $encodedKeyword = urlencode($keyword);
        switch (strtolower($engine))
        {
            case 'google':
                return self::GOOGLE_BASE_URL . self::GOOGLE_SEARCH_PARAM . $encodedKeyword
                       . self::GOOGLE_QUERY_SEPARATOR . self::GOOGLE_OFFSET_PARAM
                       . self::normalizeOffset($engine, $offset);
                break;
            case 'bing':
                return self::BING_BASE_URL . self::BING_SEARCH_PARAM . $encodedKeyword
                       . self::BING_QUERY_SEPARATOR . self::BING_OFFSET_PARAM
                       . self::normalizeOffset($engine, $offset);
                break;
            case 'yahoo':
                return self::YAHOO_BASE_URL . self::YAHOO_SEARCH_PARAM . $encodedKeyword
                       . self::YAHOO_QUERY_SEPARATOR . self::YAHOO_OFFSET_PARAM
                       . self::normalizeOffset($engine, $offset);
                break;
            case 'ask':
                return self::ASK_BASE_URL . self::ASK_SEARCH_PARAM . $encodedKeyword
                       . self::ASK_QUERY_SEPARATOR . self::ASK_OFFSET_PARAM
                       . self::normalizeOffset($engine, $offset);
                break;
            default:
                throw new \Franzip\SerpScraper\Exceptions\UnsupportedEngineException('Unknown or unsupported Search Engine.');
                break;
        }
    }

    /**
     * Make offsets consistent through different engines.
     * @param  string $engine
     * @param  int $offset
     * @return string
     */
    private static function normalizeOffset($engine, $offset)
    {
        switch (strtolower($engine)) {
            case 'google':
                return $offset * 10;
            case 'bing':
                return ($offset * 10) + 1;
            case 'yahoo':
                return ($offset * 10) + 1;
            case 'ask':
                return $offset + 1;
        }
    }

    private function __construct() {}
}
