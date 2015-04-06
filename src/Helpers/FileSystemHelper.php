<?php

namespace Franzip\SerpScraper\Helpers;

/**
 * Namespace Filesystem related methods.
 *
 * @package  SerpScraper
 */
class FileSystemHelper
{
    const FILENAME_SEPARATOR = "_";

    /**
     * Generate a suitable array key for a serialized Serp Page.
     * @param  string $engine
     * @param  string $keyword
     * @param  int    $pageNumber
     * @param  string $age
     * @param  string $format
     * @return string
     */
    public static function generateArrKey($engine, $keyword, $pageNumber, $age, $format)
    {
        $filename  = $engine . self::FILENAME_SEPARATOR;
        $filename .= $keyword . self::FILENAME_SEPARATOR;
        $filename .= $pageNumber . self::FILENAME_SEPARATOR;
        $filename .= $age . self::FILENAME_SEPARATOR;
        $filename .= $format;
        return strtolower($filename);
    }

    /**
     * Obtain a filename by appending the file extension to the array key.
     * Map vendor_keyword_page_date_json to vendor_keyword_page_date.json
     * @param  string $arrKey
     * @return string
     */
    public static function generateFileName($arrKey)
    {
        return preg_replace('/_(\w+)$/', '.$1', $arrKey);
    }

    /**
     * Prevent usage of same folder for fetcher, serializer and output dir.
     * @param  string $fetcherCache
     * @param  string $serializerCache
     * @param  string $outDir
     * @return bool
     */
    public static function preventCacheCollision($outDir,
                                                 $fetcherCacheDir,
                                                 $serializerCacheDir)
    {
        return $serializerCacheDir != $fetcherCacheDir
               && $fetcherCacheDir != $outDir
               && $outDir != $serializerCacheDir;
    }

    /**
     * Create a folder if it isn't there yet.
     * @param string $dir
     */
    public static function setUpDir($dir)
    {
        if (!self::folderExists($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    /**
     * Check if a folder exists.
     * @param  string $dir
     * @return bool
     */
    public static function folderExists($dir)
    {
        return file_exists($dir) && is_dir($dir);
    }

    private function __construct() {}
}
