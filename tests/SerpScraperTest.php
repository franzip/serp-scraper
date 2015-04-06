<?php

namespace Franzip\SerpScraper\SerpScraper\Test;
use Franzip\SerpScraper\SerpScraperBuilder as Builder;
use Franzip\SerpScraper\Helpers\TestHelper;
use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;

class SerpScraperGettersSettersTest extends PHPUnit_Framework_TestCase
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

    public function testWithDefaultArgs()
    {
        $googleScraper = Builder::create($this->engines[0], array(array('foo')));
        $this->assertEquals($googleScraper->getOutDir(), 'out');
        $this->assertEquals($googleScraper->getFetcherCacheDir(), 'fetcher_cache');
        $this->assertEquals($googleScraper->getSerializerCacheDir(), 'serializer_cache');
        $this->assertEquals($googleScraper->getCacheTTL(), 24);
        $this->assertEquals($googleScraper->getRequestDelay(), 500);
        $this->assertEquals($googleScraper->getKeywords(), array('foo'));
        $this->assertEquals($googleScraper->getThrottler()->getName(), 'http_requests');
        $this->assertEquals($googleScraper->getThrottler()->getGlobalThreshold(), 15);
        $this->assertEquals($googleScraper->getThrottler()->getMetric(), 'hrs');
        $this->assertEquals($googleScraper->getThrottler()->getMetricFactor(), 1);
        $this->assertNull($googleScraper->getThrottler()->getComponentThreshold());
        $this->assertEquals($googleScraper->getThrottler()->getComponents(),
                            array('foo' => 0));
        $this->assertFalse($googleScraper->setOutDir(2));
        $this->assertTrue($googleScraper->setOutDir('foobar1'));
        $this->assertFalse($googleScraper->setFetcherCacheDir(3));
        $this->assertFalse($googleScraper->setFetcherCacheDir('foobar1'));
        $this->assertFalse($googleScraper->setOutDir('fetcher_cache'));
        $this->assertTrue($googleScraper->setFetcherCacheDir('foobar2'));
        $this->assertTrue(file_exists('foobar1') && is_dir('foobar1'));
        $this->assertTrue(file_exists('foobar2') && is_dir('foobar2'));
        $this->assertFalse($googleScraper->setCacheTTL('bar'));
        $this->assertTrue($googleScraper->setCacheTTL(200));
        $this->assertEquals($googleScraper->getFetcher()->getCacheTTL(), 200);
        $this->assertFalse($googleScraper->setRequestDelay('foo'));
        $this->assertTrue($googleScraper->setRequestDelay(100));
        $this->assertEquals($googleScraper->getOutDir(), 'foobar1');
        $this->assertEquals($googleScraper->getFetcherCacheDir(), 'foobar2');
        $this->assertEquals($googleScraper->getCacheTTL(), 200);
        $this->assertEquals($googleScraper->getRequestDelay(), 100);
        $this->assertFalse($googleScraper->addKeyword(3));
        $this->assertTrue($googleScraper->addKeyword('foobaz'));
        $this->assertTrue($googleScraper->addKeyword('baz'));
        $this->assertEquals($googleScraper->getKeywords(),
                            array('foo', 'foobaz', 'baz'));
        $this->assertEquals($googleScraper->getThrottler()->getComponents(),
                            array('foo' => 0, 'foobaz' => 0, 'baz' => 0));
        $this->assertFalse($googleScraper->addKeywords(array()));
        $this->assertFalse($googleScraper->addKeywords(array('foo' => 1)));
        $this->assertTrue($googleScraper->addKeywords(array('foo', 'baz', 'bar')));
        $this->assertEquals($googleScraper->getKeywords(),
                            array('foo', 'foobaz', 'baz', 'bar'));
        $this->assertTrue($googleScraper->removeKeyword('foobaz'));
        $this->assertTrue($googleScraper->removeKeyword('foo'));
        $this->assertEquals($googleScraper->getKeywords(), array('baz', 'bar'));
        $this->assertEquals($googleScraper->getThrottler()->getComponents(),
                            array('foo' => 0, 'foobaz' => 0, 'baz' => 0, 'bar' => 0));

        $askScraper = Builder::create($this->engines[1], array(array('foo')));
        $this->assertEquals($askScraper->getOutDir(), 'out');
        $this->assertEquals($askScraper->getFetcherCacheDir(), 'fetcher_cache');
        $this->assertEquals($askScraper->getSerializerCacheDir(), 'serializer_cache');
        $this->assertEquals($askScraper->getCacheTTL(), 24);
        $this->assertEquals($askScraper->getRequestDelay(), 500);
        $this->assertEquals($askScraper->getKeywords(), array('foo'));
        $this->assertEquals($askScraper->getThrottler()->getName(), 'http_requests');
        $this->assertEquals($askScraper->getThrottler()->getGlobalThreshold(), 15);
        $this->assertEquals($askScraper->getThrottler()->getMetric(), 'hrs');
        $this->assertEquals($askScraper->getThrottler()->getMetricFactor(), 1);
        $this->assertNull($askScraper->getThrottler()->getComponentThreshold());
        $this->assertEquals($askScraper->getThrottler()->getComponents(),
                            array('foo' => 0));
        $this->assertFalse($askScraper->setOutDir(2));
        $this->assertTrue($askScraper->setOutDir('foobar3'));
        $this->assertFalse($askScraper->setFetcherCacheDir(3));
        $this->assertTrue($askScraper->setFetcherCacheDir('foobar4'));
        $this->assertTrue(file_exists('foobar3') && is_dir('foobar3'));
        $this->assertTrue(file_exists('foobar4') && is_dir('foobar4'));
        $this->assertFalse($askScraper->setCacheTTL('bar'));
        $this->assertTrue($askScraper->setCacheTTL(200));
        $this->assertEquals($askScraper->getFetcher()->getCacheTTL(), 200);
        $this->assertFalse($askScraper->setRequestDelay('foo'));
        $this->assertTrue($askScraper->setRequestDelay(100));
        $this->assertEquals($askScraper->getOutDir(), 'foobar3');
        $this->assertEquals($askScraper->getFetcherCacheDir(), 'foobar4');
        $this->assertEquals($askScraper->getCacheTTL(), 200);
        $this->assertEquals($askScraper->getRequestDelay(), 100);
        $this->assertFalse($askScraper->addKeyword(3));
        $this->assertTrue($askScraper->addKeyword('foobaz'));
        $this->assertTrue($askScraper->addKeyword('baz'));
        $this->assertEquals($askScraper->getKeywords(),
                            array('foo', 'foobaz', 'baz'));
        $this->assertEquals($askScraper->getThrottler()->getComponents(),
                            array('foo' => 0, 'foobaz' => 0, 'baz' => 0));
        $this->assertFalse($askScraper->addKeywords(array('foo', 'bar' => 1)));
        $this->assertTrue($askScraper->addKeywords(array('bar', 'foobar', 'barfoo')));
        $this->assertTrue($askScraper->removeKeyword('foobaz'));
        $this->assertTrue($askScraper->removeKeyword('foo'));
        $this->assertEquals($askScraper->getKeywords(),
                            array('baz', 'bar', 'foobar', 'barfoo'));
        $this->assertEquals($askScraper->getThrottler()->getComponents(),
                            array('foo' => 0, 'foobaz' => 0, 'baz' => 0,
                                  'bar' => 0, 'foobar' => 0, 'barfoo' => 0));

        $bingScraper = Builder::create($this->engines[2], array(array('foo')));
        $this->assertEquals($bingScraper->getOutDir(), 'out');
        $this->assertEquals($bingScraper->getFetcherCacheDir(), 'fetcher_cache');
        $this->assertEquals($bingScraper->getSerializerCacheDir(), 'serializer_cache');
        $this->assertEquals($bingScraper->getCacheTTL(), 24);
        $this->assertEquals($bingScraper->getRequestDelay(), 500);
        $this->assertEquals($bingScraper->getKeywords(), array('foo'));
        $this->assertEquals($bingScraper->getThrottler()->getName(), 'http_requests');
        $this->assertEquals($bingScraper->getThrottler()->getGlobalThreshold(), 15);
        $this->assertEquals($bingScraper->getThrottler()->getMetric(), 'hrs');
        $this->assertEquals($bingScraper->getThrottler()->getMetricFactor(), 1);
        $this->assertNull($bingScraper->getThrottler()->getComponentThreshold());
        $this->assertEquals($bingScraper->getThrottler()->getComponents(),
                            array('foo' => 0));
        $this->assertFalse($bingScraper->setOutDir(2));
        $this->assertTrue($bingScraper->setOutDir('foobar5'));
        $this->assertFalse($bingScraper->setFetcherCacheDir(3));
        $this->assertTrue($bingScraper->setFetcherCacheDir('foobar6'));
        $this->assertTrue(file_exists('foobar5') && is_dir('foobar5'));
        $this->assertTrue(file_exists('foobar6') && is_dir('foobar6'));
        $this->assertFalse($bingScraper->setCacheTTL('bar'));
        $this->assertTrue($bingScraper->setCacheTTL(200));
        $this->assertEquals($bingScraper->getFetcher()->getCacheTTL(), 200);
        $this->assertFalse($bingScraper->setRequestDelay('foo'));
        $this->assertTrue($bingScraper->setRequestDelay(100));
        $this->assertEquals($bingScraper->getOutDir(), 'foobar5');
        $this->assertEquals($bingScraper->getFetcherCacheDir(), 'foobar6');
        $this->assertEquals($bingScraper->getCacheTTL(), 200);
        $this->assertEquals($bingScraper->getRequestDelay(), 100);
        $this->assertFalse($bingScraper->addKeyword(3));
        $this->assertTrue($bingScraper->addKeyword('foobaz'));
        $this->assertTrue($bingScraper->addKeyword('baz'));
        $this->assertEquals($bingScraper->getKeywords(),
                            array('foo', 'foobaz', 'baz'));
        $this->assertEquals($bingScraper->getThrottler()->getComponents(),
                            array('foo' => 0, 'foobaz' => 0, 'baz' => 0));
        $this->assertTrue($bingScraper->addKeywords(array('barfoo')));
        $this->assertTrue($bingScraper->removeKeyword('foobaz'));
        $this->assertTrue($bingScraper->removeKeyword('foo'));
        $this->assertEquals($bingScraper->getKeywords(), array('baz', 'barfoo'));
        $this->assertEquals($bingScraper->getThrottler()->getComponents(),
                            array('foo' => 0, 'foobaz' => 0, 'baz' => 0, 'barfoo' => 0));

        $yahooScraper = Builder::create($this->engines[3], array(array('foo')));
        $this->assertEquals($yahooScraper->getOutDir(), 'out');
        $this->assertEquals($yahooScraper->getFetcherCacheDir(), 'fetcher_cache');
        $this->assertEquals($yahooScraper->getSerializerCacheDir(), 'serializer_cache');
        $this->assertEquals($yahooScraper->getCacheTTL(), 24);
        $this->assertEquals($yahooScraper->getRequestDelay(), 500);
        $this->assertEquals($yahooScraper->getKeywords(), array('foo'));
        $this->assertEquals($yahooScraper->getThrottler()->getName(), 'http_requests');
        $this->assertEquals($yahooScraper->getThrottler()->getGlobalThreshold(), 15);
        $this->assertEquals($yahooScraper->getThrottler()->getMetric(), 'hrs');
        $this->assertEquals($yahooScraper->getThrottler()->getMetricFactor(), 1);
        $this->assertNull($yahooScraper->getThrottler()->getComponentThreshold());
        $this->assertEquals($yahooScraper->getThrottler()->getComponents(),
                            array('foo' => 0));
        $this->assertFalse($yahooScraper->setOutDir(2));
        $this->assertTrue($yahooScraper->setOutDir('foobar7'));
        $this->assertFalse($yahooScraper->setFetcherCacheDir(3));
        $this->assertTrue($yahooScraper->setFetcherCacheDir('foobar8'));
        $this->assertTrue(file_exists('foobar7') && is_dir('foobar7'));
        $this->assertTrue(file_exists('foobar8') && is_dir('foobar8'));
        $this->assertFalse($yahooScraper->setCacheTTL('bar'));
        $this->assertTrue($yahooScraper->setCacheTTL(200));
        $this->assertFalse($yahooScraper->setRequestDelay('foo'));
        $this->assertTrue($yahooScraper->setRequestDelay(100));
        $this->assertEquals($yahooScraper->getOutDir(), 'foobar7');
        $this->assertEquals($yahooScraper->getFetcherCacheDir(), 'foobar8');
        $this->assertEquals($yahooScraper->getCacheTTL(), 200);
        $this->assertEquals($yahooScraper->getFetcher()->getCacheTTL(), 200);
        $this->assertEquals($yahooScraper->getRequestDelay(), 100);
        $this->assertFalse($yahooScraper->addKeyword(3));
        $this->assertFalse($yahooScraper->addKeyword('foo'));
        $this->assertTrue($yahooScraper->addKeyword('baz'));
        $this->assertEquals($yahooScraper->getKeywords(), array('foo', 'baz'));
        $this->assertEquals($yahooScraper->getThrottler()->getComponents(),
                            array('foo' => 0, 'baz' => 0));
        $this->assertFalse($yahooScraper->removeKeyword('foobaz'));
        $this->assertTrue($yahooScraper->removeKeyword('foo'));
        $this->assertEquals($yahooScraper->getKeywords(), array('baz'));
        $this->assertEquals($yahooScraper->getThrottler()->getComponents(),
                            array('foo' => 0, 'baz' => 0));
    }

    public function testWithCustomArgs()
    {
        $googleScraper = Builder::create($this->engines[0],
                                         array(array('foobam ', '  foobaz', 'baz'),
                                         'foo', 'bar', 'baz', 48, 1000));
        $this->assertEquals($googleScraper->getOutDir(), 'foo');
        $this->assertEquals($googleScraper->getFetcherCacheDir(), 'bar');
        $this->assertEquals($googleScraper->getSerializerCacheDir(), 'baz');
        $this->assertTrue(file_exists('foo') && is_dir('foo'));
        $this->assertTrue(file_exists('bar') && is_dir('bar'));
        $this->assertTrue(file_exists('baz') && is_dir('baz'));
        $this->assertEquals($googleScraper->getCacheTTL(), 48);
        $this->assertEquals($googleScraper->getRequestDelay(), 1000);
        $this->assertEquals($googleScraper->getKeywords(),
                            array('foobam', 'foobaz', 'baz'));
        $this->assertEquals($googleScraper->getThrottler()->getName(), 'http_requests');
        $this->assertEquals($googleScraper->getThrottler()->getGlobalThreshold(), 15);
        $this->assertEquals($googleScraper->getThrottler()->getMetric(), 'hrs');
        $this->assertEquals($googleScraper->getThrottler()->getMetricFactor(), 1);
        $this->assertNull($googleScraper->getThrottler()->getComponentThreshold());
        $this->assertEquals($googleScraper->getThrottler()->getComponents(),
                            array('foobam' => 0, 'foobaz' => 0, 'baz' => 0));
        $this->assertFalse($googleScraper->setOutDir(2));
        $this->assertTrue($googleScraper->setOutDir('foobar1'));
        $this->assertFalse($googleScraper->setFetcherCacheDir(3));
        $this->assertTrue($googleScraper->setFetcherCacheDir('foobar2'));
        $this->assertTrue(file_exists('foobar1') && is_dir('foobar1'));
        $this->assertTrue(file_exists('foobar2') && is_dir('foobar2'));
        $this->assertFalse($googleScraper->setCacheTTL('bar'));
        $this->assertTrue($googleScraper->setCacheTTL(200));
        $this->assertFalse($googleScraper->setRequestDelay('foo'));
        $this->assertTrue($googleScraper->setRequestDelay(100));
        $this->assertEquals($googleScraper->getOutDir(), 'foobar1');
        $this->assertEquals($googleScraper->getFetcherCacheDir(), 'foobar2');
        $this->assertEquals($googleScraper->getCacheTTL(), 200);
        $this->assertEquals($googleScraper->getRequestDelay(), 100);
        $this->assertFalse($googleScraper->addKeyword(3));
        $this->assertTrue($googleScraper->addKeyword("\t    foo"));
        $this->assertFalse($googleScraper->addKeyword('baz'));
        $this->assertEquals($googleScraper->getKeywords(),
                            array('foobam', 'foobaz', 'baz', 'foo'));
        $this->assertEquals($googleScraper->getThrottler()->getComponents(),
                            array('foo' => 0, 'foobam' => 0, 'foobaz' => 0, 'baz' => 0));
        $this->assertTrue($googleScraper->removeKeyword('foobaz'));
        $this->assertTrue($googleScraper->removeKeyword('foo'));
        $this->assertEquals($googleScraper->getKeywords(), array('foobam', 'baz'));
        $this->assertTrue($googleScraper->addKeywords(array('foobaz', 'foo')));
        $this->assertEquals($googleScraper->getKeywords(),
                            array('foobam', 'baz', 'foobaz', 'foo'));

        $askScraper = Builder::create($this->engines[1], array(array('foobaz'),
                                      'fooz', 'barz', 'bazz', 72, 1500));
        $this->assertEquals($askScraper->getOutDir(), 'fooz');
        $this->assertEquals($askScraper->getFetcherCacheDir(), 'barz');
        $this->assertEquals($askScraper->getSerializerCacheDir(), 'bazz');
        $this->assertTrue(file_exists('fooz') && is_dir('fooz'));
        $this->assertTrue(file_exists('barz') && is_dir('barz'));
        $this->assertTrue(file_exists('bazz') && is_dir('bazz'));
        $this->assertEquals($askScraper->getCacheTTL(), 72);
        $this->assertEquals($askScraper->getRequestDelay(), 1500);
        $this->assertEquals($askScraper->getKeywords(), array('foobaz'));
        $this->assertEquals($askScraper->getThrottler()->getName(), 'http_requests');
        $this->assertEquals($askScraper->getThrottler()->getGlobalThreshold(), 15);
        $this->assertEquals($askScraper->getThrottler()->getMetric(), 'hrs');
        $this->assertEquals($askScraper->getThrottler()->getMetricFactor(), 1);
        $this->assertNull($askScraper->getThrottler()->getComponentThreshold());
        $this->assertEquals($askScraper->getThrottler()->getComponents(),
                            array('foobaz' => 0));

        $bingScraper = Builder::create($this->engines[2],
                                       array(
                                             array('foobam', 'foobaz'),
                                             'foobar', 'barfoo', 'bazfoo', 148,
                                             100));
        $this->assertEquals($bingScraper->getOutDir(), 'foobar');
        $this->assertEquals($bingScraper->getFetcherCacheDir(), 'barfoo');
        $this->assertEquals($bingScraper->getSerializerCacheDir(), 'bazfoo');
        $this->assertTrue(file_exists('foobar') && is_dir('foobar'));
        $this->assertTrue(file_exists('barfoo') && is_dir('barfoo'));
        $this->assertTrue(file_exists('bazfoo') && is_dir('bazfoo'));
        $this->assertEquals($bingScraper->getCacheTTL(), 148);
        $this->assertEquals($bingScraper->getRequestDelay(), 100);
        $this->assertEquals($bingScraper->getKeywords(), array('foobam', 'foobaz'));
        $this->assertEquals($bingScraper->getThrottler()->getName(), 'http_requests');
        $this->assertEquals($bingScraper->getThrottler()->getGlobalThreshold(), 15);
        $this->assertEquals($bingScraper->getThrottler()->getMetric(), 'hrs');
        $this->assertEquals($bingScraper->getThrottler()->getMetricFactor(), 1);
        $this->assertNull($bingScraper->getThrottler()->getComponentThreshold());
        $this->assertEquals($bingScraper->getThrottler()->getComponents(),
                            array('foobam' => 0, 'foobaz' => 0));

        $yahooScraper = Builder::create($this->engines[1],
                                        array(
                                              array('  foobam', '  foobaz'),
                                              'foo', 'bar', 'baz', 48, 1000));
        $this->assertEquals($yahooScraper->getOutDir(), 'foo');
        $this->assertEquals($yahooScraper->getFetcherCacheDir(), 'bar');
        $this->assertEquals($yahooScraper->getSerializerCacheDir(), 'baz');
        $this->assertEquals($yahooScraper->getCacheTTL(), 48);
        $this->assertEquals($yahooScraper->getRequestDelay(), 1000);
        $this->assertEquals($yahooScraper->getKeywords(), array('foobam', 'foobaz'));
        $this->assertEquals($yahooScraper->getThrottler()->getName(), 'http_requests');
        $this->assertEquals($yahooScraper->getThrottler()->getGlobalThreshold(), 15);
        $this->assertEquals($yahooScraper->getThrottler()->getMetric(), 'hrs');
        $this->assertEquals($yahooScraper->getThrottler()->getMetricFactor(), 1);
        $this->assertNull($yahooScraper->getThrottler()->getComponentThreshold());
        $this->assertEquals($yahooScraper->getThrottler()->getComponents(),
                            array('foobam' => 0, 'foobaz' => 0));
    }
}

class ScrapingTest extends PHPUnit_Framework_TestCase
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

    public function testKeyToUrlMapping()
    {
        $googleScraper = Builder::create($this->engines[0],
                                         array(array('foo', 'baz')));
        $mapKeywordsToUrls = TestHelper::getMethod('mapKeywordsToUrls', 'Google');
        $this->assertEquals(
                            $mapKeywordsToUrls->invokeArgs($googleScraper, array(1, 'foo')),
                            array(
                                  "foo" => array("http://www.google.com/search?q=foo&start=0")
                                  )
                            );

        $this->assertEquals(
                            $mapKeywordsToUrls->invokeArgs($googleScraper, array(1, $googleScraper->getKeywords())),
                            array(
                                  "foo" => array("http://www.google.com/search?q=foo&start=0"),
                                  "baz" => array("http://www.google.com/search?q=baz&start=0")
                                  )
                            );

        $this->assertEquals(
                            $mapKeywordsToUrls->invokeArgs($googleScraper, array(2, $googleScraper->getKeywords())),
                            array(
                                  "foo" => array("http://www.google.com/search?q=foo&start=0",
                                                "http://www.google.com/search?q=foo&start=10"),
                                  "baz" => array("http://www.google.com/search?q=baz&start=0",
                                                 "http://www.google.com/search?q=baz&start=10")
                                  )
                            );

        $askScraper = Builder::create($this->engines[1], array(array('foobar', 'baz')));
        $mapKeywordsToUrls = TestHelper::getMethod('mapKeywordsToUrls', 'Ask');
        $this->assertEquals($mapKeywordsToUrls->invokeArgs($askScraper, array(1, 'foobar')),
                            array(
                                  "foobar" => array("http://us.ask.com/web?q=foobar&page=1")
                                  )
                            );

        $this->assertEquals($mapKeywordsToUrls->invokeArgs($askScraper, array(1, $askScraper->getKeywords())),
                            array(
                                  "foobar" => array("http://us.ask.com/web?q=foobar&page=1"),
                                  "baz"    => array("http://us.ask.com/web?q=baz&page=1")
                                  )
                            );

        $this->assertEquals($mapKeywordsToUrls->invokeArgs($askScraper, array(2, $askScraper->getKeywords())),
                            array("foobar" => array("http://us.ask.com/web?q=foobar&page=1",
                                                    "http://us.ask.com/web?q=foobar&page=2"),
                                  "baz"    => array("http://us.ask.com/web?q=baz&page=1",
                                                    "http://us.ask.com/web?q=baz&page=2")
                                  )
                            );

        $bingScraper = Builder::create($this->engines[2], array(array('bazfoo', 'foobaz')));
        $mapKeywordsToUrls = TestHelper::getMethod('mapKeywordsToUrls', 'Bing');
        $this->assertEquals($mapKeywordsToUrls->invokeArgs($bingScraper, array(1, 'bazfoo')),
                            array(
                                  "bazfoo" => array("http://www.bing.com/search?q=bazfoo&first=1")
                                  )
                            );

        $this->assertEquals($mapKeywordsToUrls->invokeArgs($bingScraper, array(1, $bingScraper->getKeywords())),
                            array(
                                  "bazfoo" => array("http://www.bing.com/search?q=bazfoo&first=1"),
                                  "foobaz" => array("http://www.bing.com/search?q=foobaz&first=1")
                                  )
                            );

        $this->assertEquals($mapKeywordsToUrls->invokeArgs($bingScraper, array(2, $bingScraper->getKeywords())),
                            array(
                                  "bazfoo" => array("http://www.bing.com/search?q=bazfoo&first=1",
                                                    "http://www.bing.com/search?q=bazfoo&first=11"),
                                  "foobaz" => array("http://www.bing.com/search?q=foobaz&first=1",
                                                    "http://www.bing.com/search?q=foobaz&first=11")
                                  )
                            );

        $yahooScraper = Builder::create($this->engines[2], array(array('foo')));
        $mapKeywordsToUrls = TestHelper::getMethod('mapKeywordsToUrls', 'Yahoo');
        $this->assertEquals($mapKeywordsToUrls->invokeArgs($googleScraper, array(1, 'foo')),
                            array(
                                  "foo" => array("https://search.yahoo.com/search?p=foo&b=1")
                                  )
                            );

        $this->assertEquals($mapKeywordsToUrls->invokeArgs($googleScraper, array(5, $yahooScraper->getKeywords())),
                            array(
                                  "foo" => array("https://search.yahoo.com/search?p=foo&b=1",
                                                 "https://search.yahoo.com/search?p=foo&b=11",
                                                 "https://search.yahoo.com/search?p=foo&b=21",
                                                 "https://search.yahoo.com/search?p=foo&b=31",
                                                 "https://search.yahoo.com/search?p=foo&b=41")
                                  )
                            );
    }

    public function testHitCounter()
    {
        $googleScraper = Builder::create($this->engines[0], array(array('foo', 'baz')));
        $mapKeywordsToUrls = TestHelper::getMethod('mapKeywordsToUrls', 'Google');
        $urlsToScrape = $mapKeywordsToUrls->invokeArgs($googleScraper,
                                                       array(1, $googleScraper->getKeywords()));
        $hitCounter = TestHelper::getMethod('hitCounter', 'Google');
        list($globalHitCount, $componentHitCount) = $hitCounter->invokeArgs($googleScraper,
                                                                            array($urlsToScrape));
        $this->assertEquals($globalHitCount, 2);
        $this->assertEquals($componentHitCount, array("foo" => 1, "baz" => 1));
        $this->assertTrue($googleScraper->addKeyword('foobaz'));
        $urlsToScrape = $mapKeywordsToUrls->invokeArgs($googleScraper,
                                                       array(2, $googleScraper->getKeywords()));
        list($globalHitCount, $componentHitCount) = $hitCounter->invokeArgs($googleScraper,
                                                                            array($urlsToScrape));
        $this->assertEquals($globalHitCount, 6);
        $this->assertEquals($componentHitCount, array("foo" => 2, "baz" => 2, "foobaz" => 2));
        $this->assertTrue($googleScraper->removeKeyword("foo"));
        $this->assertTrue($googleScraper->removeKeyword("baz"));
        $urlsToScrape = $mapKeywordsToUrls->invokeArgs($googleScraper,
                                                       array(3, $googleScraper->getKeywords()));
        list($globalHitCount, $componentHitCount) = $hitCounter->invokeArgs($googleScraper,
                                                                            array($urlsToScrape));
        $this->assertEquals($globalHitCount, 3);
        $this->assertEquals($componentHitCount, array("foobaz" => 3));

        $askScraper = Builder::create($this->engines[1], array(array('foobar', 'baz')));
        $mapKeywordsToUrls = TestHelper::getMethod('mapKeywordsToUrls', 'Ask');
        $urlsToScrape = $mapKeywordsToUrls->invokeArgs($googleScraper,
                                                       array(1, $askScraper->getKeywords()));
        $hitCounter = TestHelper::getMethod('hitCounter', 'Ask');
        list($globalHitCount, $componentHitCount) = $hitCounter->invokeArgs($askScraper,
                                                                            array($urlsToScrape));
        $this->assertEquals($globalHitCount, 2);
        $this->assertEquals($componentHitCount, array("foobar" => 1, "baz" => 1));
        $this->assertTrue($askScraper->addKeyword('foobaz'));
        $urlsToScrape = $mapKeywordsToUrls->invokeArgs($askScraper,
                                                       array(2, $askScraper->getKeywords()));
        list($globalHitCount, $componentHitCount) = $hitCounter->invokeArgs($askScraper,
                                                                            array($urlsToScrape));
        $this->assertEquals($globalHitCount, 6);
        $this->assertEquals($componentHitCount, array("foobar" => 2, "baz" => 2, "foobaz" => 2));
        $this->assertTrue($askScraper->removeKeyword("foobar"));
        $this->assertTrue($askScraper->removeKeyword("baz"));
        $urlsToScrape = $mapKeywordsToUrls->invokeArgs($askScraper,
                                                       array(3, $askScraper->getKeywords()));
        list($globalHitCount, $componentHitCount) = $hitCounter->invokeArgs($askScraper,
                                                                            array($urlsToScrape));
        $this->assertEquals($globalHitCount, 3);
        $this->assertEquals($componentHitCount, array("foobaz" => 3));

        $bingScraper = Builder::create($this->engines[2], array(array('bazfoo', 'foobaz')));
        $mapKeywordsToUrls = TestHelper::getMethod('mapKeywordsToUrls', 'Bing');
        $urlsToScrape = $mapKeywordsToUrls->invokeArgs($bingScraper,
                                                       array(1, $bingScraper->getKeywords()));
        $hitCounter = TestHelper::getMethod('hitCounter', 'Bing');
        list($globalHitCount, $componentHitCount) = $hitCounter->invokeArgs($bingScraper,
                                                                            array($urlsToScrape));
        $this->assertEquals($globalHitCount, 2);
        $this->assertEquals($componentHitCount, array("bazfoo" => 1, "foobaz" => 1));
        $this->assertTrue($bingScraper->addKeyword('foo'));
        $urlsToScrape = $mapKeywordsToUrls->invokeArgs($bingScraper,
                                                       array(2, $bingScraper->getKeywords()));
        list($globalHitCount, $componentHitCount) = $hitCounter->invokeArgs($bingScraper,
                                                                            array($urlsToScrape));
        $this->assertEquals($globalHitCount, 6);
        $this->assertEquals($componentHitCount, array("bazfoo" => 2, "foo" => 2, "foobaz" => 2));
        $this->assertTrue($bingScraper->removeKeyword("foo"));
        $this->assertTrue($bingScraper->removeKeyword("bazfoo"));
        $urlsToScrape = $mapKeywordsToUrls->invokeArgs($bingScraper,
                                                       array(3, $bingScraper->getKeywords()));
        list($globalHitCount, $componentHitCount) = $hitCounter->invokeArgs($bingScraper,
                                                                            array($urlsToScrape));
        $this->assertEquals($globalHitCount, 3);
        $this->assertEquals($componentHitCount, array("foobaz" => 3));

        $yahooScraper = Builder::create($this->engines[3], array(array('foo')));
        $mapKeywordsToUrls = TestHelper::getMethod('mapKeywordsToUrls', 'Yahoo');
        $hitCounter = TestHelper::getMethod('hitCounter', 'Yahoo');
        $urlsToScrape = $mapKeywordsToUrls->invokeArgs($bingScraper,
                                                       array(1, $yahooScraper->getKeywords()));
        list($globalHitCount, $componentHitCount) = $hitCounter->invokeArgs($yahooScraper,
                                                                            array($urlsToScrape));
        $this->assertEquals($globalHitCount, 1);
        $this->assertEquals($componentHitCount, array("foo" => 1));
        $this->assertTrue($yahooScraper->addKeyword('foobaz'));
        $urlsToScrape = $mapKeywordsToUrls->invokeArgs($yahooScraper,
                                                       array(2, $yahooScraper->getKeywords()));
        list($globalHitCount, $componentHitCount) = $hitCounter->invokeArgs($yahooScraper,
                                                                            array($urlsToScrape));
        $this->assertEquals($globalHitCount, 4);
        $this->assertEquals($componentHitCount, array("foo" => 2, "foobaz" => 2));
        $this->assertTrue($yahooScraper->removeKeyword("foobaz"));
        $urlsToScrape = $mapKeywordsToUrls->invokeArgs($yahooScraper,
                                                       array(3, $yahooScraper->getKeywords()));
        list($globalHitCount, $componentHitCount) = $hitCounter->invokeArgs($yahooScraper,
                                                                            array($urlsToScrape));
        $this->assertEquals($globalHitCount, 3);
        $this->assertEquals($componentHitCount, array("foo" => 3));
    }

    public function testHitChecker()
    {
        $googleScraper = Builder::create($this->engines[0], array(array('foo', 'baz')));
        $mapKeywordsToUrls = TestHelper::getMethod('mapKeywordsToUrls', 'Google');
        $hitCounter = TestHelper::getMethod('hitCounter', 'Google');
        $hitChecker = TestHelper::getMethod('hitChecker', 'Google');
        $urlsToScrape = $mapKeywordsToUrls->invokeArgs($googleScraper,
                                                       array(1, $googleScraper->getKeywords()));
        list($globalHitCount, $componentHitCount) = $hitCounter->invokeArgs($googleScraper,
                                                                            array($urlsToScrape));
        $this->assertTrue($hitChecker->invokeArgs($googleScraper,
                                                  array($globalHitCount, $componentHitCount)));
        $urlsToScrape = $mapKeywordsToUrls->invokeArgs($googleScraper,
                                                       array(7, $googleScraper->getKeywords()));
        list($globalHitCount, $componentHitCount) = $hitCounter->invokeArgs($googleScraper,
                                                                            array($urlsToScrape));
        $this->assertTrue($hitChecker->invokeArgs($googleScraper,
                                                  array($globalHitCount, $componentHitCount)));
        $urlsToScrape = $mapKeywordsToUrls->invokeArgs($googleScraper,
                                                       array(8, $googleScraper->getKeywords()));
        list($globalHitCount, $componentHitCount) = $hitCounter->invokeArgs($googleScraper,
                                                                            array($urlsToScrape));
        $this->assertFalse($hitChecker->invokeArgs($googleScraper,
                                                   array($globalHitCount, $componentHitCount)));
        $googleScraper->getThrottler()->stop();
        $this->assertTrue($googleScraper->getThrottler()->setComponentThreshold(3));
        $urlsToScrape = $mapKeywordsToUrls->invokeArgs($googleScraper,
                                                       array(3, $googleScraper->getKeywords()));
        list($globalHitCount, $componentHitCount) = $hitCounter->invokeArgs($googleScraper,
                                                                            array($urlsToScrape));
        $this->assertTrue($hitChecker->invokeArgs($googleScraper,
                                                  array($globalHitCount, $componentHitCount)));
        $urlsToScrape = $mapKeywordsToUrls->invokeArgs($googleScraper,
                                                       array(4, $googleScraper->getKeywords()));
        list($globalHitCount, $componentHitCount) = $hitCounter->invokeArgs($googleScraper,
                                                                            array($urlsToScrape));
        $this->assertFalse($hitChecker->invokeArgs($googleScraper,
                                                   array($globalHitCount, $componentHitCount)));

        $askScraper = Builder::create($this->engines[0], array(array('foo', 'baz', 'foobaz')));
        $mapKeywordsToUrls = TestHelper::getMethod('mapKeywordsToUrls', 'Ask');
        $hitCounter = TestHelper::getMethod('hitCounter', 'Ask');
        $hitChecker = TestHelper::getMethod('hitChecker', 'Ask');
        $askScraper->getThrottler()->stop();
        $this->assertTrue($askScraper->getThrottler()->setGlobalThreshold(100));
        $urlsToScrape = $mapKeywordsToUrls->invokeArgs($askScraper,
                                                       array(30, $askScraper->getKeywords()));
        list($globalHitCount, $componentHitCount) = $hitCounter->invokeArgs($askScraper,
                                                                            array($urlsToScrape));
        $this->assertTrue($hitChecker->invokeArgs($askScraper,
                                                  array($globalHitCount, $componentHitCount)));
        $urlsToScrape = $mapKeywordsToUrls->invokeArgs($askScraper,
                                                       array(40, $askScraper->getKeywords()));
        list($globalHitCount, $componentHitCount) = $hitCounter->invokeArgs($askScraper,
                                                                            array($urlsToScrape));
        $this->assertFalse($hitChecker->invokeArgs($askScraper,
                                                   array($globalHitCount, $componentHitCount)));
        $this->assertTrue($askScraper->getThrottler()->setComponentThreshold(20));
        $urlsToScrape = $mapKeywordsToUrls->invokeArgs($askScraper, array(15, $askScraper->getKeywords()));
        list($globalHitCount, $componentHitCount) = $hitCounter->invokeArgs($askScraper,
                                                                            array($urlsToScrape));
        $this->assertTrue($hitChecker->invokeArgs($askScraper,
                                                  array($globalHitCount, $componentHitCount)));
        $urlsToScrape = $mapKeywordsToUrls->invokeArgs($askScraper,
                                                       array(21, $askScraper->getKeywords()));
        list($globalHitCount, $componentHitCount) = $hitCounter->invokeArgs($askScraper,
                                                                            array($urlsToScrape));
        $this->assertFalse($hitChecker->invokeArgs($askScraper, array($globalHitCount, $componentHitCount)));

        $bingScraper = Builder::create($this->engines[0], array(array('foo', 'baz', 'foobaz')));
        $mapKeywordsToUrls = TestHelper::getMethod('mapKeywordsToUrls', 'Bing');
        $hitCounter = TestHelper::getMethod('hitCounter', 'Bing');
        $hitChecker = TestHelper::getMethod('hitChecker', 'Bing');
        $urlsToScrape = $mapKeywordsToUrls->invokeArgs($bingScraper,
                                                       array(4, $bingScraper->getKeywords()));
        list($globalHitCount, $componentHitCount) = $hitCounter->invokeArgs($bingScraper,
                                                                            array($urlsToScrape));
        $this->assertTrue($hitChecker->invokeArgs($bingScraper,
                                                  array($globalHitCount, $componentHitCount)));
        $urlsToScrape = $mapKeywordsToUrls->invokeArgs($bingScraper,
                                                       array(5, $bingScraper->getKeywords()));
        list($globalHitCount, $componentHitCount) = $hitCounter->invokeArgs($bingScraper,
                                                                            array($urlsToScrape));
        $this->assertFalse($hitChecker->invokeArgs($bingScraper,
                                                   array($globalHitCount, $componentHitCount)));
    }

    public function testScrapingFilter()
    {
        $googleScraper = Builder::create($this->engines[0], array(array('foo', 'baz')));
        $this->assertFalse($googleScraper->scrape(0));
        $this->assertFalse($googleScraper->scrape(''));
        $this->assertFalse($googleScraper->scrape('foobaz'));
        $this->assertFalse($googleScraper->scrape('foo', 's'));
        $this->assertFalse($googleScraper->scrape('foo', 0));
        $this->assertFalse($googleScraper->scrape('foo', 1, 'UTC', 1));
        $this->assertFalse($googleScraper->scrape('foo', 16));
        $this->assertFalse($googleScraper->scrape('foo', 100));
        $googleScraper->getThrottler()->stop();
        $this->assertTrue($googleScraper->getThrottler()->setGlobalThreshold(10));
        $this->assertFalse($googleScraper->scrape('foo', 11));
        $this->assertFalse($googleScraper->scrape('foo', 10));
        $this->assertTrue($googleScraper->getThrottler()->setComponentThreshold(4));
        $this->assertFalse($googleScraper->scrape('foo', 5));
        $this->assertFalse($googleScraper->scrapeAll(8));
        $this->assertFalse($googleScraper->scrapeAll('foo'));
        $this->assertFalse($googleScraper->scrapeAll(2, 'foo'));
        $this->assertTrue($googleScraper->removeKeyword("foo"));
        $this->assertTrue($googleScraper->removeKeyword("baz"));
        $this->assertFalse($googleScraper->scrapeAll(1));

        $askScraper = Builder::create($this->engines[1], array(array('foo', 'baz')));
        $this->assertFalse($askScraper->scrape(0));
        $this->assertFalse($askScraper->scrape(''));
        $this->assertFalse($askScraper->scrape('foobaz'));
        $this->assertFalse($askScraper->scrape('foo', 's'));
        $this->assertFalse($askScraper->scrape('foo', 0));
        $this->assertFalse($askScraper->scrape('foo', 1, 'UTC', 1));
        $this->assertFalse($askScraper->scrape('foo', 16));
        $this->assertFalse($askScraper->scrape('foo', 100));
        $askScraper->getThrottler()->stop();
        $this->assertTrue($askScraper->getThrottler()->setGlobalThreshold(10));
        $this->assertFalse($askScraper->scrape('foo', 11));
        $this->assertFalse($askScraper->scrape('foo', 10));
        $this->assertTrue($askScraper->getThrottler()->setComponentThreshold(4));
        $this->assertFalse($askScraper->scrape('foo', 5));
        $this->assertFalse($askScraper->scrapeAll(8));
        $this->assertFalse($askScraper->scrapeAll('foo'));
        $this->assertFalse($askScraper->scrapeAll(2, 'foo'));
        $this->assertTrue($askScraper->removeKeyword("foo"));
        $this->assertTrue($askScraper->removeKeyword("baz"));
        $this->assertFalse($askScraper->scrapeAll(1));

        $bingScraper = Builder::create($this->engines[2], array(array('foo', 'baz')));
        $this->assertFalse($bingScraper->scrape(0));
        $this->assertFalse($bingScraper->scrape(''));
        $this->assertFalse($bingScraper->scrape('foobaz'));
        $this->assertFalse($bingScraper->scrape('foo', 's'));
        $this->assertFalse($bingScraper->scrape('foo', 0));
        $this->assertFalse($bingScraper->scrape('foo', 1, 'UTC', 1));
        $this->assertFalse($bingScraper->scrape('foo', 16));
        $this->assertFalse($bingScraper->scrape('foo', 100));
        $bingScraper->getThrottler()->stop();
        $this->assertTrue($bingScraper->getThrottler()->setGlobalThreshold(10));
        $this->assertFalse($bingScraper->scrape('foo', 11));
        $this->assertFalse($bingScraper->scrape('foo', 10));
        $this->assertTrue($bingScraper->getThrottler()->setComponentThreshold(4));
        $this->assertFalse($bingScraper->scrape('foo', 5));
        $this->assertFalse($bingScraper->scrapeAll(8));
        $this->assertFalse($bingScraper->scrapeAll('foo'));
        $this->assertFalse($bingScraper->scrapeAll(2, 'foo'));
        $this->assertTrue($bingScraper->removeKeyword("foo"));
        $this->assertTrue($bingScraper->removeKeyword("baz"));
        $this->assertFalse($bingScraper->scrapeAll(1));

        $yahooScraper = Builder::create($this->engines[3], array(array('foo', 'baz')));
        $this->assertFalse($yahooScraper->scrape(0));
        $this->assertFalse($yahooScraper->scrape(''));
        $this->assertFalse($yahooScraper->scrape('foobaz'));
        $this->assertFalse($yahooScraper->scrape('foo', 's'));
        $this->assertFalse($yahooScraper->scrape('foo', 0));
        $this->assertFalse($yahooScraper->scrape('foo', 1, 'UTC', 1));
        $this->assertFalse($yahooScraper->scrape('foo', 16));
        $this->assertFalse($yahooScraper->scrape('foo', 100));
        $yahooScraper->getThrottler()->stop();
        $this->assertTrue($yahooScraper->getThrottler()->setGlobalThreshold(10));
        $this->assertFalse($yahooScraper->scrape('foo', 11));
        $this->assertFalse($yahooScraper->scrape('foo', 10));
        $this->assertTrue($yahooScraper->getThrottler()->setComponentThreshold(4));
        $this->assertFalse($yahooScraper->scrape('foo', 5));
        $this->assertFalse($yahooScraper->scrapeAll(8));
        $this->assertFalse($yahooScraper->scrapeAll('foo'));
        $this->assertFalse($yahooScraper->scrapeAll(2, 'foo'));
        $this->assertTrue($yahooScraper->removeKeyword("foo"));
        $this->assertTrue($yahooScraper->removeKeyword("baz"));
        $this->assertFalse($yahooScraper->scrapeAll(1));
    }

    public function testScrapeArgs()
    {
        $googleScraper = Builder::create($this->engines[0], array(array('foo')));
        $this->assertTrue($googleScraper->removeKeyword('foo'));
        $this->assertFalse($googleScraper->scrape(1));
        $this->assertTrue($googleScraper->addKeywords(array("foo", "bar", "baz")));
        $this->assertFalse($googleScraper->scrape('foobar'));
        $this->assertFalse($googleScraper->scrape('foo', 'baz'));
        $this->assertFalse($googleScraper->scrape('foo', 2, 'UTC', 'baz'));
        $this->assertFalse($googleScraper->scrape('foo', 2, 'UTC', true, 'baz'));
        $this->assertFalse($googleScraper->scrapeAll('foobar'));
        $this->assertFalse($googleScraper->scrapeAll(2, 'foobar'));
        $this->assertFalse($googleScraper->scrapeAll(2, true, 'foobar'));

        $askScraper = Builder::create($this->engines[1], array(array('foo')));
        $this->assertTrue($askScraper->removeKeyword('foo'));
        $this->assertFalse($askScraper->scrape(2));
        $this->assertTrue($askScraper->addKeywords(array("foo", "bar", "baz")));
        $this->assertFalse($askScraper->scrape('foobar'));
        $this->assertFalse($askScraper->scrape('foo', 'baz'));
        $this->assertFalse($askScraper->scrape('foo', 2, 'UTC', 'baz'));
        $this->assertFalse($askScraper->scrape('foo', 2, true, 'baz'));
        $this->assertFalse($askScraper->scrapeAll('foobar'));
        $this->assertFalse($askScraper->scrapeAll(2, 'foobar'));
        $this->assertFalse($askScraper->scrapeAll(2, true, 'foobar'));

        $bingScraper = Builder::create($this->engines[2], array(array('foo')));
        $this->assertTrue($bingScraper->removeKeyword('foo'));
        $this->assertFalse($bingScraper->scrape(3));
        $this->assertTrue($bingScraper->addKeywords(array("foo", "bar", "baz")));
        $this->assertFalse($bingScraper->scrape('foobar'));
        $this->assertFalse($bingScraper->scrape('foo', 'baz'));
        $this->assertFalse($bingScraper->scrape('foo', 2, 'UTC', 'baz'));
        $this->assertFalse($bingScraper->scrape('foo', 2, true, 'baz'));
        $this->assertFalse($bingScraper->scrapeAll('foobar'));
        $this->assertFalse($bingScraper->scrapeAll(2, 'foobar'));
        $this->assertFalse($bingScraper->scrapeAll(2, true, 'foobar'));

        $yahooScraper = Builder::create($this->engines[2], array(array('foo')));
        $this->assertTrue($yahooScraper->removeKeyword('foo'));
        $this->assertFalse($yahooScraper->scrape(4));
        $this->assertTrue($yahooScraper->addKeywords(array("foo", "bar", "baz")));
        $this->assertFalse($yahooScraper->scrape('foobar'));
        $this->assertFalse($yahooScraper->scrape('foo', 'baz'));
        $this->assertFalse($yahooScraper->scrape('foo', 2, 'UTC', 'baz'));
        $this->assertFalse($yahooScraper->scrape('foo', 2, true, 'baz'));
        $this->assertFalse($yahooScraper->scrapeAll('foobar'));
        $this->assertFalse($yahooScraper->scrapeAll(2, 'foobar'));
        $this->assertFalse($yahooScraper->scrapeAll(2, true, 'foobar'));
    }

    public function testScrape()
    {
        $parser = new \Seld\JsonLint\JsonParser();

        $googleScraper = Builder::create($this->engines[0],
                                         array(array('foo', 'baz'), 'google'));
        $outDir        = $googleScraper->getOutDir();
        $this->assertFalse($googleScraper->scrape('bar'));
        $this->assertFalse($googleScraper->scrape('baz', 100));
        $this->assertFalse($googleScraper->scrape('baz', 1, 'baz'));
        $this->assertFalse($googleScraper->scrape('baz', 1, true, 'foobad'));
        $this->assertFalse($googleScraper->scrape('baz', 1, true, 'UTC', 'faz'));
        $this->assertFalse($googleScraper->serialize('json'));
        $this->assertTrue($googleScraper->scrape('foo', 2, true, 'Europe/Berlin'));
        $this->assertCount(2, $googleScraper->getFetchedPages());
        $this->assertCount(1, $googleScraper->getKeywords());
        $this->assertTrue($googleScraper->scrape('baz', 2, true));
        $this->assertCount(4, $googleScraper->getFetchedPages());
        $this->assertCount(0, $googleScraper->getKeywords());
        $this->assertFalse($googleScraper->scrapeAll());
        $this->assertTrue($googleScraper->addKeywords(array('foobaz', 'foobar')));
        $this->assertTrue($googleScraper->scrapeAll(2, true, 'America/Los_Angeles'));
        $this->assertCount(8, $googleScraper->getFetchedPages());
        $this->assertCount(0, $googleScraper->getKeywords());
        $this->assertFalse($googleScraper->serialize('baz'));
        $this->assertTrue($googleScraper->serialize('json', true));
        $this->assertCount(0, $googleScraper->getFetchedPages());
        $this->assertCount(8, $googleScraper->getSerializedPages());
        $toCheck = array_map('Franzip\SerpScraper\Helpers\FileSystemHelper::generateFileName',
                             array_keys($googleScraper->getSerializedPages()));
        $this->assertTrue($googleScraper->save(true));
        for ($i = 0; $i < count($toCheck); $i++) {
            $json = file_get_contents($outDir . DIRECTORY_SEPARATOR . $toCheck[$i]);
            $this->assertNull($parser->lint($json));
        }
        $this->assertTrue($googleScraper->addKeywords(array('foo bad')));
        $this->assertTrue($googleScraper->scrapeAll(3, true));
        $this->assertCount(3, $googleScraper->getFetchedPages());
        $this->assertTrue($googleScraper->serialize('xml', true));
        $this->assertCount(0, $googleScraper->getFetchedPages());
        $this->assertCount(3, $googleScraper->getSerializedPages());
        $toCheck = array_map('Franzip\SerpScraper\Helpers\FileSystemHelper::generateFileName',
                             array_keys($googleScraper->getSerializedPages()));
        $this->assertTrue($googleScraper->save(true));
        for ($i = 0; $i < count($toCheck); $i++) {
            $xml = new \XMLReader();
            $xml->open($outDir . DIRECTORY_SEPARATOR . $toCheck[$i]);
            $xml->setParserProperty(\XMLReader::VALIDATE, true);
            $this->assertTrue($xml->isValid());
        }

        $askScraper = Builder::create($this->engines[1],
                                      array(array('foo', 'baz'), 'ask'));
        $outDir     = $askScraper->getOutDir();
        $this->assertFalse($askScraper->scrape('bar'));
        $this->assertFalse($askScraper->scrape('baz', 100));
        $this->assertFalse($askScraper->scrape('baz', 1, 'baz'));
        $this->assertFalse($askScraper->scrape('baz', 1, true, 'foobad'));
        $this->assertFalse($askScraper->scrape('baz', 1, true, 'UTC', 'faz'));
        $this->assertTrue($askScraper->scrape('foo', 2, true, 'Europe/Rome'));
        $this->assertCount(2, $askScraper->getFetchedPages());
        $this->assertCount(1, $askScraper->getKeywords());
        $this->assertTrue($askScraper->scrape('baz', 2, true));
        $this->assertCount(4, $askScraper->getFetchedPages());
        $this->assertCount(0, $askScraper->getKeywords());
        $this->assertFalse($askScraper->scrapeAll());
        $this->assertTrue($askScraper->addKeywords(array('foobaz', 'foobar')));
        $this->assertTrue($askScraper->scrapeAll(2, true, 'America/Los_Angeles'));
        $this->assertCount(8, $askScraper->getFetchedPages());
        $this->assertCount(0, $askScraper->getKeywords());
        $this->assertFalse($askScraper->serialize('baz'));
        $this->assertTrue($askScraper->serialize('xml', true));
        $this->assertCount(0, $askScraper->getFetchedPages());
        $this->assertCount(8, $askScraper->getSerializedPages());
        $toCheck = array_map('Franzip\SerpScraper\Helpers\FileSystemHelper::generateFileName',
                             array_keys($askScraper->getSerializedPages()));
        $this->assertTrue($askScraper->save(true));
        $this->assertCount(0, $askScraper->getSerializedPages());
        for ($i = 0; $i < count($toCheck); $i++) {
            $xml = new \XMLReader();
            $xml->open($outDir . DIRECTORY_SEPARATOR . $toCheck[$i]);
            $xml->setParserProperty(\XMLReader::VALIDATE, true);
            $this->assertTrue($xml->isValid());
        }
        $this->assertTrue($askScraper->addKeywords(array('foobaz')));
        $this->assertTrue($askScraper->scrapeAll(3, true));
        $this->assertTrue($askScraper->serialize('json', true));
        $toCheck = array_map('Franzip\SerpScraper\Helpers\FileSystemHelper::generateFileName',
                             array_keys($askScraper->getSerializedPages()));
        $this->assertTrue($askScraper->save(true));
        for ($i = 0; $i < count($toCheck); $i++) {
            $json = file_get_contents($outDir . DIRECTORY_SEPARATOR . $toCheck[$i]);
            $this->assertNull($parser->lint($json));
        }

        $bingScraper = Builder::create($this->engines[2],
                                       array(array('foo', 'baz'), 'bing'));
        $outDir      = $bingScraper->getOutDir();
        $this->assertFalse($bingScraper->scrape('bar'));
        $this->assertFalse($bingScraper->scrape('baz', 100));
        $this->assertFalse($bingScraper->scrape('baz', 1, 'baz'));
        $this->assertFalse($bingScraper->scrape('baz', 1, true, 'foobad'));
        $this->assertFalse($bingScraper->scrape('baz', 1, true, 'UTC', 'faz'));
        $this->assertFalse($bingScraper->serialize('json'));
        $this->assertTrue($bingScraper->scrape('foo', 2, true, 'Europe/Berlin'));
        $this->assertCount(2, $bingScraper->getFetchedPages());
        $this->assertCount(1, $bingScraper->getKeywords());
        $this->assertTrue($bingScraper->scrape('baz', 2, true));
        $this->assertCount(4, $bingScraper->getFetchedPages());
        $this->assertCount(0, $bingScraper->getKeywords());
        $this->assertFalse($bingScraper->scrapeAll());
        $this->assertTrue($bingScraper->addKeywords(array('foobaz', 'foobar')));
        $this->assertTrue($bingScraper->scrapeAll(2, true, 'America/Los_Angeles'));
        $this->assertCount(8, $bingScraper->getFetchedPages());
        $this->assertCount(0, $bingScraper->getKeywords());
        $this->assertFalse($bingScraper->serialize('baz'));
        $this->assertTrue($bingScraper->serialize('json', true));
        $this->assertCount(0, $bingScraper->getFetchedPages());
        $this->assertCount(8, $bingScraper->getSerializedPages());
        $toCheck = array_map('Franzip\SerpScraper\Helpers\FileSystemHelper::generateFileName',
                             array_keys($bingScraper->getSerializedPages()));
        $this->assertTrue($bingScraper->save(true));
        for ($i = 0; $i < count($toCheck); $i++) {
            $json = file_get_contents($outDir . DIRECTORY_SEPARATOR . $toCheck[$i]);
            $this->assertNull($parser->lint($json));
        }
        $this->assertTrue($bingScraper->addKeywords(array('foo bad')));
        $this->assertTrue($bingScraper->scrapeAll(2, true));
        $this->assertCount(2, $bingScraper->getFetchedPages());
        $this->assertTrue($bingScraper->serialize('xml', true));
        $this->assertCount(0, $bingScraper->getFetchedPages());
        $this->assertCount(2, $bingScraper->getSerializedPages());
        $toCheck = array_map('Franzip\SerpScraper\Helpers\FileSystemHelper::generateFileName',
                             array_keys($bingScraper->getSerializedPages()));
        $this->assertTrue($bingScraper->save(true));
        for ($i = 0; $i < count($toCheck); $i++) {
            $xml = new \XMLReader();
            $xml->open($outDir . DIRECTORY_SEPARATOR . $toCheck[$i]);
            $xml->setParserProperty(\XMLReader::VALIDATE, true);
            $this->assertTrue($xml->isValid());
        }

        $yahooScraper = Builder::create($this->engines[3],
                                        array(array('foo', 'baz'), 'yahoo'));
        $outDir       = $yahooScraper->getOutDir();
        $this->assertFalse($yahooScraper->scrape('bar'));
        $this->assertFalse($yahooScraper->scrape('baz', 100));
        $this->assertFalse($yahooScraper->scrape('baz', 1, 'baz'));
        $this->assertFalse($yahooScraper->scrape('baz', 1, true, 'foobad'));
        $this->assertFalse($yahooScraper->scrape('baz', 1, true, 'UTC', 'faz'));
        $this->assertTrue($yahooScraper->scrape('foo', 2, true, 'Europe/Rome'));
        $this->assertCount(2, $yahooScraper->getFetchedPages());
        $this->assertCount(1, $yahooScraper->getKeywords());
        $this->assertTrue($yahooScraper->scrape('baz', 2, true));
        $this->assertCount(4, $yahooScraper->getFetchedPages());
        $this->assertCount(0, $yahooScraper->getKeywords());
        $this->assertFalse($yahooScraper->scrapeAll());
        $this->assertTrue($yahooScraper->addKeywords(array('foobaz', 'foobar')));
        $this->assertTrue($yahooScraper->scrapeAll(2, true, 'America/Los_Angeles'));
        $this->assertCount(8, $yahooScraper->getFetchedPages());
        $this->assertCount(0, $yahooScraper->getKeywords());
        $this->assertFalse($yahooScraper->serialize('baz'));
        $this->assertTrue($yahooScraper->serialize('xml', true));
        $this->assertCount(0, $yahooScraper->getFetchedPages());
        $this->assertCount(8, $yahooScraper->getSerializedPages());
        $toCheck = array_map('Franzip\SerpScraper\Helpers\FileSystemHelper::generateFileName',
                             array_keys($yahooScraper->getSerializedPages()));
        $this->assertTrue($yahooScraper->save(true));
        $this->assertCount(0, $yahooScraper->getSerializedPages());
        for ($i = 0; $i < count($toCheck); $i++) {
            $xml = new \XMLReader();
            $xml->open($outDir . DIRECTORY_SEPARATOR . $toCheck[$i]);
            $xml->setParserProperty(\XMLReader::VALIDATE, true);
            $this->assertTrue($xml->isValid());
        }
        $this->assertTrue($yahooScraper->addKeywords(array('foobaz')));
        $this->assertTrue($yahooScraper->scrapeAll(3, true));
        $this->assertTrue($yahooScraper->serialize('json', true));
        $toCheck = array_map('Franzip\SerpScraper\Helpers\FileSystemHelper::generateFileName',
                             array_keys($yahooScraper->getSerializedPages()));
        $this->assertTrue($yahooScraper->save(true));
        for ($i = 0; $i < count($toCheck); $i++) {
            $json = file_get_contents($outDir . DIRECTORY_SEPARATOR . $toCheck[$i]);
            $this->assertNull($parser->lint($json));
        }
    }
}
