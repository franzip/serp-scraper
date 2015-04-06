<?php

namespace Franzip\SerpScraper\SerpScraperBuilder\Test;
use Franzip\SerpScraper\SerpScraperBuilder as Builder;
use Franzip\SerpScraper\Helpers\TestHelper;
use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;

class BuilderInvalidEnginesTest extends PHPUnit_Framework_TestCase
{
    protected $invalidEngines;

    protected function setUp()
    {
        $invalidEngines = array('', ' ', 'foo', 'bar');
        $this->invalidEngines = $invalidEngines;
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\UnsupportedEngineException
     * @expectedExceptionMessage Unknown or unsupported Search Engine.
     */
    public function testInvalidEngineArgument1()
    {
        $foo = Builder::create($this->invalidEngines[0]);
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\UnsupportedEngineException
     * @expectedExceptionMessage Unknown or unsupported Search Engine.
     */
    public function testInvalidEngineArgument2()
    {
        $foo = Builder::create($this->invalidEngines[1]);
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\UnsupportedEngineException
     * @expectedExceptionMessage Unknown or unsupported Search Engine.
     */
    public function testInvalidEngineArgument3()
    {
        $foo = Builder::create($this->invalidEngines[2]);
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\UnsupportedEngineException
     * @expectedExceptionMessage Unknown or unsupported Search Engine.
     */
    public function testInvalidEngineArgument4()
    {
        $foo = Builder::create($this->invalidEngines[3]);
    }
}

class BuilderInvalidArgsTest extends PHPUnit_Framework_TestCase
{
    protected $invalidKeywords;
    protected $invalidTime;
    protected $invalidDirs;
    protected $engines;

    protected function setUp()
    {
        $invalidDirs = array(1, '', false);
        $invalidTime = array('', 'foo', ' ', false);
        $invalidKeywords = array(array(''), array(' '), array(false), array(2),
                           array(str_repeat('foo', 100)), array('foo' => 'bar'),
                           array('foo', 'baz', 'fobaz', 'bar' => 'baz'),
                           array('foo', 'baz', 0));
        $engines = array('gOOgLe', 'aSk', 'BIng', 'yAHOo');
        $this->invalidKeywords = $invalidKeywords;
        $this->invalidTime = $invalidTime;
        $this->invalidDirs = $invalidDirs;
        $this->engines = $engines;
    }

    protected function tearDown()
    {
        TestHelper::cleanMess();
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage Invalid SerializableSerpPage $keywords: please supply a sequential non-empty array of strings.
     */
    public function testEmptyKeywordsArr()
    {
        $googleScraper = Builder::create($this->engines[0], array(array()));
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage Invalid SerializableSerpPage $outDir: please supply a non empty string.
     */
    public function testInvalidOutDir()
    {
        $googleScraper = Builder::create($this->engines[0], array(array('foo'), $this->invalidDirs[0]));
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage Invalid SerializableSerpPage $outDir: please supply a non empty string.
     */
    public function testInvalidOutDir1()
    {
        $askScraper = Builder::create($this->engines[1], array(array('foo'), $this->invalidDirs[1]));
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage Invalid SerializableSerpPage $fetcherCacheDir: please supply a non empty string.
     */
    public function testInvalidCacheDir()
    {
        $bingScraper = Builder::create($this->engines[2], array(array('foo'), 'foo', $this->invalidDirs[2]));
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage Invalid SerializableSerpPage $fetcherCacheDir: please supply a non empty string.
     */
    public function testInvalidCacheDir2()
    {
        $yahooScraper = Builder::create($this->engines[3], array(array('foo'), 'bar', $this->invalidDirs[3]));
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage Invalid SerializableSerpPage $serializerCacheDir: please supply a non empty string.
     */
    public function testInvalidCacheDir3()
    {
        $yahooScraper = Builder::create($this->engines[3], array(array('foo'), 'bar', 'baz', $this->invalidDirs[0]));
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage Invalid SerializableSerpPage $serializerCacheDir: please supply a non empty string.
     */
    public function testInvalidCacheDir4()
    {
        $googleScraper = Builder::create($this->engines[0], array(array('foo'), 'baz', 'bar', $this->invalidDirs[1]));
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage Invalid SerializableSerpPage $outDir, $fetcherCacheDir, $serializerCacheDir: cannot share the same folder for different caches. Please supply different folders path for different caches.
     */
    public function testEqualCacheDir()
    {
        $googleScraper = Builder::create($this->engines[0], array(array('foo'), 'baz', 'bar', 'bar'));
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage Invalid SerializableSerpPage $cacheTTL: please supply a positive integer.
     */
    public function testInvalidCacheTTL()
    {
        $askScraper = Builder::create($this->engines[1], array(array('foo'), 'baz', 'bar', 'foo', $this->invalidTime[0]));
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage Invalid SerializableSerpPage $cacheTTL: please supply a positive integer.
     */
    public function testInvalidCacheTTL1()
    {
        $bingScraper = Builder::create($this->engines[2], array(array('foo'), 'baz', 'bar', 'foo', $this->invalidTime[1]));
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage Invalid SerializableSerpPage $cacheTTL: please supply a positive integer.
     */
    public function testInvalidCacheTTL2()
    {
        $bingScraper = Builder::create($this->engines[2], array(array('foo'), 'baz', 'bar', 'foo', $this->invalidTime[2]));
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage Invalid SerializableSerpPage $cacheTTL: please supply a positive integer.
     */
    public function testInvalidCacheTTL3()
    {
        $yahooScraper = Builder::create($this->engines[3], array(array('foo'), 'baz', 'bar', 'foo', $this->invalidTime[3]));
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage Invalid SerializableSerpPage $requestDelay: please supply a positive integer.
     */
    public function testInvalidDelay()
    {
        $googleScraper = Builder::create($this->engines[0], array(array('foo'), 'baz', 'bar', 'foobar', 24, $this->invalidTime[0]));
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage Invalid SerializableSerpPage $requestDelay: please supply a positive integer.
     */
    public function testInvalidDelay1()
    {
        $googleScraper = Builder::create($this->engines[0], array(array('foo'), 'baz', 'bar', 'foobar', 24, $this->invalidTime[1]));
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage Invalid SerializableSerpPage $requestDelay: please supply a positive integer.
     */
    public function testInvalidDelay2()
    {
        $googleScraper = Builder::create($this->engines[0], array(array('foo'), 'baz', 'bar', 'foobar', 24, $this->invalidTime[2]));
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage Invalid SerializableSerpPage $requestDelay: please supply a positive integer.
     */
    public function testInvalidDelay3()
    {
        $googleScraper = Builder::create($this->engines[0], array(array('foo'), 'baz', 'bar', 'foobar', 24, $this->invalidTime[3]));
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage Invalid SerializableSerpPage $keywords: please supply a sequential non-empty array of strings.
     */
    public function testInvalidKeywords()
    {
        $googleScraper = Builder::create($this->engines[0], array($this->invalidKeywords[0], 'baz', 'bar', 'bad', 24, 500));
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage Invalid SerializableSerpPage $keywords: please supply a sequential non-empty array of strings.
     */
    public function testInvalidKeywords1()
    {
        $googleScraper = Builder::create($this->engines[0], array($this->invalidKeywords[1], 'baz', 'bar', 'bad', 24, 500));
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage Invalid SerializableSerpPage $keywords: please supply a sequential non-empty array of strings.
     */
    public function testInvalidKeywords2()
    {
        $askScraper = Builder::create($this->engines[1], array($this->invalidKeywords[2], 'baz', 'bar', 'bad', 24, 500));
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage Invalid SerializableSerpPage $keywords: please supply a sequential non-empty array of strings.
     */
    public function testInvalidKeywords3()
    {
        $bingScraper = Builder::create($this->engines[0], array($this->invalidKeywords[3], 'baz', 'bar', 'bad', 24, 500));
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage Invalid SerializableSerpPage $keywords: please supply a sequential non-empty array of strings.
     */
    public function testInvalidKeywords4()
    {
        $yahooScraper = Builder::create($this->engines[0], array($this->invalidKeywords[4], 'baz', 'bar', 'bad', 24, 500));
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage Invalid SerializableSerpPage $keywords: please supply a sequential non-empty array of strings.
     */
    public function testInvalidKeywords5()
    {
        $yahooScraper = Builder::create($this->engines[0], array($this->invalidKeywords[5], 'baz', 'bar', 'bad', 24, 500));
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage Invalid SerializableSerpPage $keywords: please supply a sequential non-empty array of strings.
     */
    public function testInvalidKeywords6()
    {
        $yahooScraper = Builder::create($this->engines[0], array($this->invalidKeywords[6], 'baz', 'bar', 'bad', 24, 500));
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage Invalid SerializableSerpPage $keywords: please supply a sequential non-empty array of strings.
     */
    public function testInvalidKeywords7()
    {
        $yahooScraper = Builder::create($this->engines[0], array($this->invalidKeywords[7], 'baz', 'bar', 'bad', 24, 500));
    }
}

class BuilderTypesTest extends PHPUnit_Framework_TestCase
{
    protected $engines;

    protected function setUp()
    {
        $engines = array('gOOgLe', 'aSk', 'BIng', 'yAHOo');
        $this->engines = $engines;
    }

    protected function tearDown()
    {
        TestHelper::cleanMess();
    }

    public function testGoogleScraper()
    {
        $googleScraper = Builder::create($this->engines[0],
                                         array(array('foo'), 'foobar', 'baz',
                                               'bazbar', 48, 200));
        $this->assertEquals(get_parent_class($googleScraper),
                            'Franzip\SerpScraper\Scrapers\SerpScraper');
        $this->assertInstanceOf('Franzip\SerpScraper\Scrapers\GoogleScraper',
                                $googleScraper);
        $this->assertInstanceOf('Franzip\Throttler\Throttler',
                                $googleScraper->getThrottler());
        $this->assertInstanceOf('Franzip\SerpFetcher\Fetchers\GoogleFetcher',
                                $googleScraper->getFetcher());
        $this->assertTrue(file_exists('foobar') && is_dir('foobar'));
        $this->assertTrue(file_exists('baz') && is_dir('baz'));
        $this->assertTrue(file_exists('bazbar') && is_dir('bazbar'));
    }

    public function testAskScraper()
    {
        $askScraper = Builder::create($this->engines[1],
                                      array(array('foo'), 'bad', 'foo', 'foobad',
                                            72, 100));
        $this->assertEquals(get_parent_class($askScraper),
                            'Franzip\SerpScraper\Scrapers\SerpScraper');
        $this->assertInstanceOf('Franzip\SerpScraper\Scrapers\AskScraper',
                                $askScraper);
        $this->assertInstanceOf('Franzip\Throttler\Throttler',
                                $askScraper->getThrottler());
        $this->assertInstanceOf('Franzip\SerpFetcher\Fetchers\AskFetcher',
                                $askScraper->getFetcher());
        $this->assertTrue(file_exists('bad') && is_dir('bad'));
        $this->assertTrue(file_exists('foobad') && is_dir('foobad'));
        $this->assertTrue(file_exists('foo') && is_dir('foo'));
    }

    public function testBingScraper()
    {
        $bingScraper = Builder::create($this->engines[2], array(array('baz')));
        $this->assertEquals(get_parent_class($bingScraper),
                            'Franzip\SerpScraper\Scrapers\SerpScraper');
        $this->assertInstanceOf('Franzip\SerpScraper\Scrapers\BingScraper',
                                $bingScraper);
        $this->assertInstanceOf('Franzip\Throttler\Throttler',
                                $bingScraper->getThrottler());
        $this->assertInstanceOf('Franzip\SerpFetcher\Fetchers\BingFetcher',
                                $bingScraper->getFetcher());
        $this->assertTrue(file_exists($bingScraper::DEFAULT_OUTPUT_DIR)
                          && is_dir($bingScraper::DEFAULT_OUTPUT_DIR));
        $this->assertTrue(file_exists($bingScraper::DEFAULT_FETCHER_CACHE_DIR)
                          && is_dir($bingScraper::DEFAULT_FETCHER_CACHE_DIR));
        $this->assertTrue(file_exists($bingScraper::DEFAULT_SERIALIZER_CACHE_DIR)
                          && is_dir($bingScraper::DEFAULT_SERIALIZER_CACHE_DIR));
    }

    public function testYahooScraper()
    {
        $yahooScraper = Builder::create($this->engines[3], array(array('baz')));
        $this->assertEquals(get_parent_class($yahooScraper),
                            'Franzip\SerpScraper\Scrapers\SerpScraper');
        $this->assertInstanceOf('Franzip\SerpScraper\Scrapers\YahooScraper',
                                $yahooScraper);
        $this->assertInstanceOf('Franzip\Throttler\Throttler',
                                $yahooScraper->getThrottler());
        $this->assertInstanceOf('Franzip\SerpFetcher\Fetchers\YahooFetcher',
                                $yahooScraper->getFetcher());
        $this->assertTrue(file_exists($yahooScraper::DEFAULT_OUTPUT_DIR)
                          && is_dir($yahooScraper::DEFAULT_OUTPUT_DIR));
        $this->assertTrue(file_exists($yahooScraper::DEFAULT_FETCHER_CACHE_DIR)
                          && is_dir($yahooScraper::DEFAULT_FETCHER_CACHE_DIR));
        $this->assertTrue(file_exists($yahooScraper::DEFAULT_SERIALIZER_CACHE_DIR)
                          && is_dir($yahooScraper::DEFAULT_SERIALIZER_CACHE_DIR));
    }
}
