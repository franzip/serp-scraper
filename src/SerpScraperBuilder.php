<?php

/**
 * SerpScraper -- Extract and serialize data scraped on Search Engine result pages.
 * @version 0.1.0
 * @author Francesco Pezzella <franzpezzella@gmail.com>
 * @link https://github.com/franzip/serp-scraper
 * @copyright Copyright 2015 Francesco Pezzella
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @package SerpScraper
 */

namespace Franzip\SerpScraper;

/**
 * SerpScraper Factory.
 *
 * @package  SerpScraper
 */
class SerpScraperBuilder
{
    // namespace constants
    const SCRAPER_CLASS_PREFIX = '\\Franzip\\SerpScraper\\Scrapers\\';
    const SCRAPER_CLASS_SUFFIX = 'Scraper';
    // implemented scrapers
    private static $supportedEngines = array('google', 'yahoo', 'bing', 'ask');

    /**
     * Return a SerpScraper implementation for a given search engine.
     * @param  string     $engine
     * @param  null|array $args
     * @return mixed
     */
    public static function create($engine, $args = null)
    {
        $engine = strtolower($engine);
        if (self::validEngine($engine)) {
            return (isset($args)) ? self::createWithArgs($engine, $args) : self::createWithArgs($engine, array());
        }
        throw new \Franzip\SerpScraper\Exceptions\UnsupportedEngineException('Unknown or unsupported Search Engine.');
    }

    /**
     * Use reflection to instantiate the right Scraper at runtime.
     * @param  string     $engine
     * @param  null|array $args
     * @return mixed
     */
    private static function createWithArgs($engine, $args)
    {
        $engineName = ucfirst($engine);
        $className  = self::SCRAPER_CLASS_PREFIX . $engineName . self::SCRAPER_CLASS_SUFFIX;
        return call_user_func_array(array(new \ReflectionClass($className),
                                    'newInstance'), $args);
    }

    /**
     * Check if there is a SerpScraper implementation for the given search engine.
     * @param  string $engine
     * @return bool
     */
    private static function validEngine($engine)
    {
        return is_string($engine) && in_array($engine, self::$supportedEngines);
    }

    /**
     * Make the class static.
     */
    private function __construct() {}
}
