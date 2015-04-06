<?php

namespace Franzip\SerpScraper\Helpers;

/**
 * Perform basic validations and cleanings on user supplied keywords.
 *
 * @package SerpScraper
 */
class KeywordValidator
{
    // use a conservative query string limit: this will also prevent filenames
    // potentially longer than 255 characters
    const ERROR_414_LIMIT = 180;

    /**
     * Return a cleaned keyword.
     * @param  string $keyword
     * @return string
     */
    public static function processKeyword($keyword)
    {
        // check for valid keyword
        if (!self::isValid($keyword))
            throw new \Franzip\SerpScraper\Exceptions\InvalidArgumentException('$keyword must be a valid string (max 180 characters).');
        // perform cleaning
        $cleanKeyword = trim($keyword);
        return strtolower(self::trimSpace($cleanKeyword));
    }

    /**
     * Perform validation on a keyword.
     * @param  string $keyword
     * @return bool
     */
    public static function isValid($keyword) {
        return is_string($keyword) && !empty($keyword)
               && !preg_match('/(^\s+$)|\n/', $keyword)
               && self::check414Error($keyword);
    }

    /**
     * Validate supplied keywords array and make sure it's sequential.
     * @param  array $keywords
     * @return bool
     */
    public static function validKeywords($keywords)
    {
        $arrayValid = is_array($keywords) && !empty($keywords)
                      && self::sequentialArray($keywords);
        return $arrayValid
               && !in_array(false, array_map('Franzip\SerpScraper\Helpers\KeywordValidator::isValid', $keywords));
    }

    /**
     * Check for sequential array.
     * @param  array $arr
     * @return bool
     */
    private static function sequentialArray($arr)
    {
        return array_keys($arr) === range(0, count($arr) - 1);
    }

    /**
     * Trim whitespace and tabs.
     * @param  string $keyword
     * @return string
     */
    private static function trimSpace($keyword) {
        return preg_replace('/(\s{2,}|\t+)/', ' ', $keyword);
    }

    /**
     * Prevent 414 HTTP errors if the keyword is too long.
     * This will also prevent yielding filenames longer than 255 characters.
     * http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
     * @param  string $keyword
     * @return bool
     */
    private static function check414Error($keyword) {
        return strlen($keyword) < self::ERROR_414_LIMIT;
    }

    private function __construct() {}
}
