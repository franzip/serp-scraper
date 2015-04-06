<?php

namespace Franzip\SerpScraper\Helpers;

/**
 * Namespace useful SerpScraper helper methods.
 *
 * @package  SerpScraper
 */
class SerpScraperHelper
{
    /**
     * Validate SerpScraper constructor arguments.
     * @param  array  $keywords
     * @param  string $outDir
     * @param  string $fetcherCacheDir
     * @param  string $serializerCacheDir
     * @param  int    $cacheTTL
     * @param  int    $requestDelay
     */
    public static function checkArgs($keywords, $outDir, $fetcherCacheDir,
                                     $serializerCacheDir, $cacheTTL, $requestDelay)
    {
        if (!\Franzip\SerpScraper\Helpers\KeywordValidator::validKeywords($keywords))
            throw new \Franzip\SerpScraper\Exceptions\InvalidArgumentException('Invalid SerializableSerpPage $keywords: please supply a sequential non-empty array of strings.');

        if (!self::validateDirName($outDir))
            throw new \Franzip\SerpScraper\Exceptions\InvalidArgumentException('Invalid SerializableSerpPage $outDir: please supply a non empty string.');

        if (!self::validateDirName($fetcherCacheDir))
            throw new \Franzip\SerpScraper\Exceptions\InvalidArgumentException('Invalid SerializableSerpPage $fetcherCacheDir: please supply a non empty string.');

        if (!self::validateDirName($serializerCacheDir))
            throw new \Franzip\SerpScraper\Exceptions\InvalidArgumentException('Invalid SerializableSerpPage $serializerCacheDir: please supply a non empty string.');

        if (!\Franzip\SerpScraper\Helpers\FileSystemHelper::preventCacheCollision($outDir, $fetcherCacheDir, $serializerCacheDir))
            throw new \Franzip\SerpScraper\Exceptions\InvalidArgumentException('Invalid SerializableSerpPage $outDir, $fetcherCacheDir, $serializerCacheDir: cannot share the same folder for different caches. Please supply different folders path for different caches.');

        if (!self::validateExpirationTime($cacheTTL))
            throw new \Franzip\SerpScraper\Exceptions\InvalidArgumentException('Invalid SerializableSerpPage $cacheTTL: please supply a positive integer.');

        if (!self::validateExpirationTime($requestDelay))
            throw new \Franzip\SerpScraper\Exceptions\InvalidArgumentException('Invalid SerializableSerpPage $requestDelay: please supply a positive integer.');
    }

    /**
     * Validate dir argument.
     * @param  string $dir
     * @return bool
     */
    public static function validateDirName($dir)
    {
        return is_string($dir) && !empty($dir);
    }

    /**
     * Validate cache expiration argument.
     * @param  int $hours
     * @return bool
     */
    public static function validateExpirationTime($hours)
    {
        return is_int($hours) && $hours > 0;
    }

    /**
     * Check for valid scraping args.
     * @param  array  $keywords
     * @param  int    $pagesToScrape
     * @param  bool   $toRemove
     * @param  string $timezone
     * @param  bool   $throttling
     * @param  array  $trackingKeywords
     * @return bool
     */
    public static function validScrapeArgs($keywords, $pagesToScrape, $toRemove,
                                           $timezone, $throttling, $trackingKeywords)
    {
        return \Franzip\SerpScraper\Helpers\KeywordValidator::validKeywords($keywords)
               && self::keywordsAllTracked($keywords, $trackingKeywords)
               && is_int($pagesToScrape)
               && $pagesToScrape > 0 && is_bool($toRemove)
               && in_array($timezone, \DateTimeZone::listIdentifiers())
               && is_bool($throttling);
    }

    /**
     * Check that a keyword is being tracked.
     * @param  string $keyword
     * @param  array  $trackingKeywords
     * @return bool
     */
    public static function keywordPresent($keyword, $trackingKeywords)
    {
        return in_array($keyword, $trackingKeywords);
    }

    /**
     * Check that all supplied keywords are being tracked.
     * @param  array $keywords
     * @param  array $trackingKeywords
     * @return bool
     */
    public static function keywordsAllTracked($keywords, $trackingKeywords)
    {
        return !in_array(false, array_map(function($keyword) use ($trackingKeywords) {
            return in_array($keyword, $trackingKeywords);
        }, $keywords));
    }

    /**
     * Extract relevant data from a SerializableSerpPage.
     * @param  SerializableSerpPage $serializablePage
     * @return array
     */
    public static function extractSerializablePageData($serializablePage)
    {
        $keyword    = $serializablePage->getKeyword();
        $pageNumber = $serializablePage->getPageNumber();
        $age        = $serializablePage->getAge()->format('Y-m-d');;
        return array($keyword, $pageNumber, $age);
    }

    private function __construct() {}
}
