<?php

/**
 * SerpScraper -- Extract, serialize and store data scraped on Search Engine result pages.
 * @version 0.1.0
 * @author Francesco Pezzella <franzpezzella@gmail.com>
 * @link https://github.com/franzip/serp-scraper
 * @copyright Copyright 2015 Francesco Pezzella
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @package SerpScraper
 */

namespace Franzip\SerpScraper\Scrapers;
use Franzip\SerpScraper\Helpers\SerpScraperHelper;
use Franzip\SerpScraper\Helpers\FileSystemHelper;
use Franzip\SerpScraper\Helpers\SerpUrlGenerator;
use Franzip\SerpScraper\Helpers\KeywordValidator;
use Franzip\SerpFetcher\SerpFetcherBuilder;
use Franzip\SerpPageSerializer\Models\SerializableSerpPage;
use Franzip\SerpPageSerializer\SerpPageSerializer;
use Franzip\Throttler\Throttler;

/**
 * Abstract class describing a SerpScraper.
 * The whole implementation is here and the concrete children classes have the sole
 * purpose to isolate different search engine scrapers and to allow dependencies
 * to work properly.
 * Once added some keywords through the constructor or through the addKeyword()
 * and addKeywords() method, the instance will be ready to scrape.
 *
 * Caching/Throttling/Delaying
 *
 * Since scraping legal status seems to be quite disputed and nobody likes
 * jerks, this implementation tries to avoid HTTP overhead using three simple
 * strategies.
 * The first is caching: the internal component of the class that
 * takes care of fetching data from the Internet (SerpFetcher) use caching, so
 * scraping the same page over and over again will result in a single
 * HTTP request (cache expiration is set to 24 hours by default).
 * The second is throttling: an internal component of the class take care of
 * capping HTTP requests (default cap is 15 requests per hour). Scraped data
 * retrieved from the cache are not counted.
 * The third is delaying: by default, a 0.5 sec delay takes place inbetween each
 * HTTP request needed to retrieve data.
 *
 *_____________________________________________________________________________
 *        Scrape          |        Serialize        |         Store           |
 *________________________|_________________________|_________________________|
 *                        |                         |                         |
 *  scrape()|scrapeAll()  |        serialize()      |         save()          |
 *                        |                         |                         |
 *________________________|_________________________|_________________________|
 *        Input           |          Input          |         Input           |
 *________________________|_________________________|_________________________|
 *                        |                         |                         |
 * HTTP request|Cache Hit |   SerializableSerpPage  |   SerializedSerpPage    |
 *                        |          array          |          array          |
 *________________________|_________________________|_________________________|
 *        Output          |          Output         |         Output          |
 *________________________|_________________________|_________________________|
 *                        |                         |                         |
 *  SerializableSerpPage  |    SerializedSerpPage   |      JSON|XML|YAML      |
 *         array          |          array          |          files          |
 *________________________|_________________________|_________________________|
 *
 *
 * Scraping
 *
 * It is possible to scrape a single keyword with the scrape() method, or to scrape
 * all the added keywords alltogether with the scrapeAll() method.
 * The scraped data will be available as SerializableSerpPage objects in the
 * $fetched array.
 *
 * Serializing
 *
 * Once scraped some keywords, it is possible to serialize the fetched data through
 * the serialize() method (only JSON, XML and YAML are supported). This method
 * will provide to serialize all the data sitting in the $fetched array, and will
 * populate the $serialized array with SerializedSerpPage objects.
 *
 * Writing serialized data to file
 *
 * Storing data to disk is easy. The save() method takes care of writing the
 * content of all SerializedSerpPage objects to different files format (XML,
 * JSON, YAML) in the specified output folder.
 *
 *
 * @package SerpScraper
 */
abstract class SerpScraper
{
    // namespacing constants
    const SCRAPERS_PREFIX = 'Franzip\SerpScraper\Scrapers\\';
    const SCRAPERS_SUFFIX = 'Scraper';
    // default results per page
    const DEFAULT_RESULTS_PER_PAGE = 10;
    // default timezone
    const DEFAULT_TIMEZONE = 'UTC';

    // Throttler
    // Allow 15 requests per hour (cache hits are not considered)
    const DEFAULT_THROTTLER_NAME                = 'http_requests';
    const DEFAULT_THROTTLER_THRESHOLD           = 15;
    const DEFAULT_THROTTLER_METRIC              = 'hrs';
    const DEFAULT_THROTTLER_METRIC_FACTOR       = 1;
    const DEFAULT_THROTTLER_COMPONENT_THRESHOLD = null;

    // SerpFetcher
    // SerpFetcher result array keys
    const SERP_FETCHER_URLS     = 'urls';
    const SERP_FETCHER_SNIPPETS = 'snippets';
    const SERP_FETCHER_TITLES   = 'titles';
    // default fetcher cache dir
    const DEFAULT_FETCHER_CACHE_DIR = 'fetcher_cache';
    // default fetcher cache time to live in hours
    const DEFAULT_FETCHER_CACHE_TTL = 24;

    // SerpPageSerializer
    // default serializer cache dir
    const DEFAULT_SERIALIZER_CACHE_DIR = 'serializer_cache';

    // SerpScraper
    // default number of pages to scrape
    const DEFAULT_PAGES_TO_SCRAPE = 1;
    // default output dir
    const DEFAULT_OUTPUT_DIR      = 'out';
    // default request delay in microseconds
    const DEFAULT_REQUEST_DELAY   = 500;

    // supported serialization format
    private static $supportedSerializationFormat = array('json', 'xml', 'yml');

    // dependencies to inject
    // Throttler component
    private $throttler;
    // SerpFetcher component
    private $fetcher;
    // SerpPageSerializer component
    private $serializer;

    // instance variables
    // output folder
    private $outDir;
    // fetcher cache folder
    private $fetcherCacheDir;
    // serializer cache folder
    private $serializerCacheDir;
    // cache expiration time
    private $cacheTTL;
    // request delay time in microseconds
    private $requestDelay;
    // keywords to scrape
    private $keywords;
    // store fetched objects
    private $fetched;
    // store serialized pages
    private $serialized;

    /**
     * Create a SerpScraper object.
     * @param array  $keywords
     * @param string $outDir
     * @param string $fetcherCacheDir
     * @param string $serializerCacheDir
     * @param int    $cacheTTL
     * @param int    $requestDelay
     */
    public function __construct($keywords,
                                $outDir             = self::DEFAULT_OUTPUT_DIR,
                                $fetcherCacheDir    = self::DEFAULT_FETCHER_CACHE_DIR,
                                $serializerCacheDir = self::DEFAULT_SERIALIZER_CACHE_DIR,
                                $cacheTTL           = self::DEFAULT_FETCHER_CACHE_TTL,
                                $requestDelay       = self::DEFAULT_REQUEST_DELAY)
    {
        // perform validation
        SerpScraperHelper::checkArgs($keywords, $outDir, $fetcherCacheDir,
                                     $serializerCacheDir, $cacheTTL, $requestDelay);
        // instance variables
        $this->outDir             = $outDir;
        $this->fetcherCacheDir    = $fetcherCacheDir;
        $this->serializerCacheDir = $serializerCacheDir;
        $this->cacheTTL           = $cacheTTL;
        $this->requestDelay       = $requestDelay;
        $this->keywords           = array();
        $this->fetched            = array();
        $this->serialized         = array();
        // normalize user input keywords
        for ($i = 0; $i < count($keywords); $i++) {
            array_push($this->keywords, KeywordValidator::processKeyword($keywords[$i]));
        }
        // set up folders
        FileSystemHelper::setUpDir($outDir);
        FileSystemHelper::setUpDir($serializerCacheDir);
        // deps injection
        $this->throttler = new Throttler(self::DEFAULT_THROTTLER_NAME,
                                         self::DEFAULT_THROTTLER_THRESHOLD,
                                         self::DEFAULT_THROTTLER_METRIC,
                                         self::DEFAULT_THROTTLER_METRIC_FACTOR,
                                         self::DEFAULT_THROTTLER_COMPONENT_THRESHOLD,
                                         $this->keywords);
        // turn on throttling
        $this->throttler->start();
        // instatiate the right fetcher at runtime (will also setup fetcher cache dir)
        $this->fetcher = SerpFetcherBuilder::create(self::runTimeClassName(),
                                                    array($this->fetcherCacheDir,
                                                          $this->cacheTTL));
        $this->serializer = new SerpPageSerializer($serializerCacheDir);
    }

    /**
     * Scrape a single keyword. This will yield as many SerializableSerpPage
     * objects as there are to scrape. Those objects will be stored in the
     * fetched array queue, ready to be serialized.
     * It is also possible to empty the keywords array by setting $toRemove to
     * true, set a specific $timezone and turn throttling off by setting $throttling
     * to false.
     * @param  string  $keyword
     * @param  int     $pagesToScrape
     * @param  bool    $toRemove
     * @param  string  $timezone
     * @param  bool    $throttling
     * @return bool
     */
    public function scrape($keyword,
                           $pagesToScrape = self::DEFAULT_PAGES_TO_SCRAPE,
                           $toRemove      = false,
                           $timezone      = self::DEFAULT_TIMEZONE,
                           $throttling    = true)
    {
        // allow scrapeAll() to reuse scrape()
        if (is_string($keyword))
            $keyword = array($keyword);
        // perform validations
        if (!SerpScraperHelper::validScrapeArgs($keyword, $pagesToScrape,
                                                $toRemove, $timezone,
                                                $throttling, $this->keywords))
            return false;
        // map keywords to array of urls ready to scrape
        $urlsToScrape = $this->mapKeywordsToUrls($pagesToScrape, $keyword);
        // check for legal operation only if throttling
        if ($throttling && !$this->allowedScrapeOperation($pagesToScrape, $urlsToScrape))
            return false;
        // avoid DateTime() annoying notices
        date_default_timezone_set($timezone);
        // loop over the keywords to scrape
        for ($i = 0; $i < count($keyword); $i++) {
            // get the current keyword
            $key = $keyword[$i];
            // scrape $pagesToScrape pages for each keyword
            for ($j = 0; $j < $pagesToScrape; $j++) {
                $pageUrl    = $urlsToScrape[$key][$j];
                $fetched    = $this->fetchPage($key, $pageUrl);
                $entries    = $this->makeEntries($fetched);
                $engine     = strtolower(self::runTimeClassName());
                $pageNumber = $j + 1;
                $age        = new \DateTime();
                $age->setTimeStamp(time());
                // construct a SerializableSerpPage and store it
                $serializablePage = new SerializableSerpPage($engine, $key, $pageUrl,
                                                             $pageNumber, $age,
                                                             $entries);
                array_push($this->fetched, $serializablePage);
                // delay inbetween requests
                usleep(self::DEFAULT_REQUEST_DELAY);
            }
            // remove the key from the queue if specified
            if ($toRemove)
                $this->removeKeyword($key);
        }

        return true;
    }

    /**
     * Scrape all the tracked keywords. This method reuses the scrape() method.
     * @param  int     $pagesToScrape
     * @param  bool    $toRemove
     * @param  string  $timezone
     * @param  bool    $throttling
     * @return bool
     */
    public function scrapeAll($pagesToScrape = self::DEFAULT_PAGES_TO_SCRAPE,
                              $toRemove      = false,
                              $timezone      = self::DEFAULT_TIMEZONE,
                              $throttling    = true)
    {
        return $this->scrape($this->keywords, $pagesToScrape, $toRemove,
                             $timezone, $throttling);
    }

    /**
     * Write all the serialized objects in the output dir.
     * If the related flag $toRemove is on, the serialized queue will be emptied.
     * @param  bool $toRemove
     * @return bool
     */
    public function save($toRemove = false)
    {
        // fail if there are no results to write to disk
        if (empty($this->serialized))
            return false;
        // loop over serialized objects
        foreach ($this->serialized as $key => $serializedObject) {
            // generate filenames
            $filename = FileSystemHelper::generateFileName($key);
            // write files in the output dir
            file_put_contents($this->getOutDir() . DIRECTORY_SEPARATOR . $filename,
                              $serializedObject->getContent());
        }

        if ($toRemove)
            $this->serialized = array();

        return true;
    }

    /**
     * Perform serialization on the SerializableSerpPage array.
     * The serialized objects will be stored in the serialized queue, waiting to
     * be written to files or to do whatever you wanna do with them.
     * If the related flag $toRemove is on, the fetched queue will be emptied.
     * @param  string $format
     * @param  bool   $toRemove
     * @return bool
     */
    public function serialize($format, $toRemove = false)
    {
        // fail if there's nothing to serialize or unsupported serialization format
        if (empty($this->fetched) || !self::supportedFormat($format))
            return false;
        // loop over the SerializableSerpPage array
        for ($i = 0; $i < count($this->fetched); $i++) {
            // get a SerializablePage object
            $serializablePage = $this->fetched[$i];
            // extract data to generate array key
            $engine = self::runTimeClassName();
            list($keyword, $pageNumber, $age) = SerpScraperHelper::extractSerializablePageData($serializablePage);
            // generate array key
            $fileName = FileSystemHelper::generateArrKey($engine, $keyword,
                                                         $pageNumber, $age,
                                                         $format);
            $this->serialized[$fileName] = $this->serializer->serialize($serializablePage,
                                                                        $format);
        }
        // empty the fetched array if specified
        if ($toRemove)
            $this->fetched = array();

        return true;
    }

    /**
     * Flush the underlying Fetcher object cache, along with the fetched and
     * serialized queues.
     */
    public function flushCache()
    {
        $this->fetcher->flushCache();
        $this->fetched    = array();
        $this->serialized = array();
    }

    /**
     * Return the underlying Fetcher object.
     * @return AskFetcher|BingFetcher|GoogleFetcher|YahooFetcher
     */
    public function getFetcher()
    {
        return $this->fetcher;
    }

    /**
     * Return the underlying Throttler object.
     * @return Throttler
     */
    public function getThrottler()
    {
        return $this->throttler;
    }

    /**
     * Return the underlying SerpPageSerializer object.
     * @return SerpPageSerializer
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * Return the serialized serp pages.
     * @return array
     */
    public function getSerializedPages()
    {
        return $this->serialized;
    }

    /**
     * Return the fetched serp pages.
     * @return array
     */
    public function getFetchedPages()
    {
        return $this->fetched;
    }

    /**
     * Get the path to the folder used to store output.
     * @return string
     */
    public function getOutDir()
    {
        return $this->outDir;
    }

    /**
     * Set the path to the folder used to store output.
     * @param   string $dir
     * @return  bool
     */
    public function setOutDir($dir)
    {
        if (SerpScraperHelper::validateDirName($dir)
            && FileSystemHelper::preventCacheCollision($dir, $this->fetcherCacheDir,
                                                       $this->serializerCacheDir)) {
            $this->outDir = $dir;
            FileSystemHelper::setUpDir($dir);
            return true;
        }
        return false;
    }

    /**
     * Get the path to the folder used to store the fetcher cache.
     * @return string
     */
    public function getFetcherCacheDir()
    {
        return $this->fetcherCacheDir;
    }

    /**
     * Set the path to the folder used to store the fetcher cache.
     * @param   string
     * @return  bool
     */
    public function setFetcherCacheDir($dir)
    {
        if (SerpScraperHelper::validateDirName($dir)
            && FileSystemHelper::preventCacheCollision($this->outDir, $dir,
                                                       $this->serializerCacheDir)) {
            $this->fetcherCacheDir = $dir;
            FileSystemHelper::setUpDir($dir);
            return true;
        }
        return false;
    }

    /**
     * Get the path to the folder used to store the serializer cache.
     * @return string
     */
    public function getSerializerCacheDir()
    {
        return $this->serializerCacheDir;
    }

    /**
     * Get the cache expiration time, in hours.
     * @return string
     */
    public function getCacheTTL()
    {
        return $this->cacheTTL;
    }

    /**
     * Set the cache expiration time, in hours.
     * @param  int
     * @return bool
     */
    public function setCacheTTL($hours)
    {
        if (SerpScraperHelper::validateExpirationTime($hours)) {
            $this->cacheTTL = $hours;
            $this->fetcher->setCacheTTL($hours);
            return true;
        }
        return false;
    }

    /**
     * Get the delay used between each request, in microseconds.
     * @return int
     */
    public function getRequestDelay()
    {
        return $this->requestDelay;
    }

    /**
     * Set the delay used between each request, in microseconds.
     * @param  int
     * @return bool
     */
    public function setRequestDelay($microseconds)
    {
        if (SerpScraperHelper::validateExpirationTime($microseconds)) {
            $this->requestDelay = $microseconds;
            return true;
        }
        return false;
    }

    /**
     * Get the array with keywords to scrape.
     * @return array
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Add a keyword to scrape. Update Throttler object accordingly.
     * @param   string $keyword
     * @return  string
     */
    public function addKeyword($keyword)
    {
        if (KeywordValidator::isValid($keyword)
            && !SerpScraperHelper::keywordPresent($keyword, $this->keywords)) {
            $cleanKeyword = KeywordValidator::processKeyword($keyword);
            array_push($this->keywords, $cleanKeyword);
            $this->throttler->stop();
            $this->throttler->addComponents($cleanKeyword);
            $this->throttler->resume();
            return true;
        }
        return false;
    }

    /**
     * Add multiple keywords to scrape.
     * @param   array $keywords
     * @return  bool
     */
    public function addKeywords($keywords)
    {
        if (KeywordValidator::validKeywords($keywords) && !empty($keywords)) {
            for ($i = 0; $i < count($keywords); $i++) {
                $this->addKeyword($keywords[$i]);
            }
            return true;
        }
        return false;
    }

    /**
     * Remove a keyword from the queue.
     * This will not remove the keyword from the underlying Throttler object, since
     * it's possible to add the keyword back again and generate new requests hits
     * that still need to be throttled.
     * @param  string $keyword
     * @return bool
     */
    public function removeKeyword($keyword)
    {
        if (SerpScraperHelper::keywordPresent($keyword, $this->keywords)) {
            $toDel = array_search($keyword, $this->keywords);
            unset($this->keywords[$toDel]);
            $this->keywords = array_values($this->keywords);
            return true;
        }
        return false;
    }

    /**
     * Fetch a SERP page and update the underlying Throttler status accordingly.
     * @param  string $keyword
     * @param  string $url
     * @return array
     */
    private function fetchPage($keyword, $url)
    {
        if (!$this->fetcher->cacheHit($url)) {
            $this->throttler->updateComponent($keyword);
        }
        return $this->fetcher->fetch($url);
    }

    /**
     * Map a fetched page returned by SerpFetcher->fetch() to an array suitable
     * for SerializableSerpPage constructor.
     * @param  array $fetchedPage
     * @return array
     */
    private function makeEntries($fetchedPage)
    {
        $entries = array();
        for ($i = 0; $i < self::DEFAULT_RESULTS_PER_PAGE; $i++) {
            // construct an entry
            $entry = array('url'     => $fetchedPage[self::SERP_FETCHER_URLS][$i],
                           'snippet' => $fetchedPage[self::SERP_FETCHER_SNIPPETS][$i],
                           'title'   => $fetchedPage[self::SERP_FETCHER_TITLES][$i]);
            // don't add padded entries
            if (!$this->paddedEntry($entry))
                array_push($entries, $entry);
        }
        return $entries;
    }

    /**
     * Detect padded entries.
     * @param  array $entry
     * @return bool
     */
    private function paddedEntry($entry)
    {
        return $entry['url'] == \Franzip\SerpFetcher\Fetchers\SerpFetcher::DEFAULT_PAD_ENTRY
               && $entry['title'] == \Franzip\SerpFetcher\Fetchers\SerpFetcher::DEFAULT_PAD_ENTRY
               && $entry['snippet'] == \Franzip\SerpFetcher\Fetchers\SerpFetcher::DEFAULT_PAD_ENTRY;
    }

    /**
     * Map keywords array to urls ready to to be scraped.
     * @param  int          $pagesToScrape
     * @param  string|array $keywords
     * @return array
     */
    private function mapKeywordsToUrls($pagesToScrape, $keywords)
    {
        $urls = array();
        if (is_string($keywords))
            $keywords = array($keywords);
        for ($i = 0; $i < count($keywords); $i++) {
            $urls[$this->keywords[$i]] = array();
            for ($j = 0; $j < $pagesToScrape; $j++) {
                $urls[$keywords[$i]][] = SerpUrlGenerator::makeUrl(self::runTimeClassName(),
                                                                   $keywords[$i], $j);
            }
        }
        return $urls;
    }

    /**
     * Check whether a scrape operation is to allow.
     * @param  int    $pagesToScrape
     * @param  string $urlsToCheck
     * @return bool
     */
    private function allowedScrapeOperation($pagesToScrape, $urlsToCheck)
    {
        if ($this->throttler->timeExpired()) {
            $this->throttler->refreshInstance();
            return true;
        }
        list($globalHitCount, $componentHitCount) = $this->hitCounter($urlsToCheck);
        return $this->hitChecker($globalHitCount, $componentHitCount);
    }

    /**
     * Compute global and per-keyword HTTP requests needed to complete a
     * scraping operation. Cached hit will be ignored.
     * @param  array $urlsArr
     * @return array
     */
    private function hitCounter($urlsArr)
    {
        $globalHitCount    = 0;
        $componentHitCount = array();
        // initialize per-component hit array
        foreach ($this->keywords as $key => $value) {
            $componentHitCount[$value] = 0;
        }
        foreach ($urlsArr as $keyword => $arr) {
            for ($i = 0; $i < count($arr); $i++) {
                // increase hits only on HTTP requests, ignore cache hit
                if (!$this->getFetcher()->cacheHit($arr[$i])) {
                    $globalHitCount += 1;
                    $componentHitCount[$keyword] += 1;
                }
            }
        }
        return array($globalHitCount, $componentHitCount);
    }

    /**
     * Check that the global and per-keyword hit counts are within the Throttler
     * thresholds.
     * @param  int    $globalHitCount
     * @param  array  $componentHitCount
     * @return bool
     */
    private function hitChecker($globalHitCount, $componentHitCount)
    {
        $componentCheck      = true;
        $globalCheck         = ($globalHitCount + $this->throttler->getCounter()) < $this->throttler->getGlobalThreshold();
        $throttlerThreshold  = $this->getThrottler()->getComponentThreshold();
        // check per-keywords hits only if per-component throttling is set
        if ($throttlerThreshold !== null) {
            $throttlerComponents = $this->getThrottler()->getComponents();
            foreach ($componentHitCount as $key => $value) {
                if ($value + $throttlerComponents[$key] > $throttlerThreshold) {
                    $componentCheck = false;
                    break;
                }
            }
        }
        return $globalCheck && $componentCheck;
    }

    /**
     * Identify the search engine at runtime from the calling class.
     * @return string
     */
    private static function runTimeClassName()
    {
        return str_replace(array(self::SCRAPERS_PREFIX, self::SCRAPERS_SUFFIX),
                           '', get_called_class());
    }

    /**
     * Check for supported serialization format.
     * @param  string $format
     * @return bool
     */
    private static function supportedFormat($format)
    {
        return in_array(strtolower($format), self::$supportedSerializationFormat);
    }
}
