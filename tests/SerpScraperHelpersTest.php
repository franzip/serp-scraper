<?php

namespace Franzip\SerpScraper\Helpers\Test;
use Franzip\SerpScraper\Helpers\KeywordValidator as Validator;
use Franzip\SerpScraper\Helpers\SerpUrlGenerator as Generator;
use Franzip\SerpScraper\Helpers\TestHelper;
use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;

class HelpersExceptionsTest extends PHPUnit_Framework_TestCase
{
    protected $invalidKeywords, $invalidEngines;

    public function setUp()
    {
        $invalidKeywords = array('', '   ', 1, false, null, str_repeat('foo', 70),
                                         "foo\n");
        $invalidEngines  = array('foo', 'bar', '', ' ');
        $this->invalidKeywords = $invalidKeywords;
        $this->invalidEngines  = $invalidEngines;
    }

    protected function tearDown()
    {
        TestHelper::cleanMess();
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage $keyword must be a valid string (max 180 characters).
     */
    public function testEmptyString()
    {
        Validator::processKeyword($this->invalidKeywords[0]);
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage $keyword must be a valid string (max 180 characters).
     */
    public function testWhiteSpaces()
    {
        Validator::processKeyword($this->invalidKeywords[1]);
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage $keyword must be a valid string (max 180 characters).
     */
    public function testInt()
    {
        Validator::processKeyword($this->invalidKeywords[2]);
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage $keyword must be a valid string (max 180 characters).
     */
    public function testBool()
    {
        Validator::processKeyword($this->invalidKeywords[3]);
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage $keyword must be a valid string (max 180 characters).
     */
    public function testNull()
    {
        Validator::processKeyword($this->invalidKeywords[4]);
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage $keyword must be a valid string (max 180 characters).
     */
    public function testLongString()
    {
        Validator::processKeyword($this->invalidKeywords[5]);
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage $keyword must be a valid string (max 180 characters).
     */
    public function testNewLine()
    {
        Validator::processKeyword($this->invalidKeywords[6]);
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\UnsupportedEngineException
     * @expectedExceptionMessage Unknown or unsupported Search Engine.
     */
    public function testInvalidEngine1()
    {
        Generator::makeUrl($this->invalidEngines[0], 'foobar', 0);
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\UnsupportedEngineException
     * @expectedExceptionMessage Unknown or unsupported Search Engine.
     */
    public function testInvalidEngine2()
    {
        Generator::makeUrl($this->invalidEngines[1], 'foobar', 0);
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\UnsupportedEngineException
     * @expectedExceptionMessage Unknown or unsupported Search Engine.
     */
    public function testInvalidEngine3()
    {
        Generator::makeUrl($this->invalidEngines[2], 'foobar', 0);
    }

    /**
     * @expectedException        \Franzip\SerpScraper\Exceptions\UnsupportedEngineException
     * @expectedExceptionMessage Unknown or unsupported Search Engine.
     */
    public function testInvalidEngine4()
    {
        Generator::makeUrl($this->invalidEngines[3], 'foobar', 0);
    }
}

class CleaningKeywordsTest extends PHPUnit_Framework_TestCase
{
    protected $keywords;

    protected function setUp()
    {
        $cleanKey   = array('foo', 'bar', 'barfoo12', str_repeat('foo', 30));
        $keyToClean = array('foo, bar', '   foo   ', "\tbar   foo   bar    foo ",
                            "\t\t foo \t bar", "\t\t\t \s//\\+?<>",
                            "\ + / ?  $  \t\t '<' \"  >");
        $this->keywords = array('cleanKey'   => $cleanKey,
                                'keyToClean' => $keyToClean);
    }

    public function testClean()
    {
        $this->assertEquals(Validator::processKeyword($this->keywords['cleanKey'][0]), 'foo');
        $this->assertEquals(Validator::processKeyword($this->keywords['cleanKey'][1]), 'bar');
        $this->assertEquals(Validator::processKeyword($this->keywords['cleanKey'][2]), 'barfoo12');
        $this->assertEquals(Validator::processKeyword($this->keywords['cleanKey'][3]), str_repeat('foo', 30));
    }

    public function testDirty()
    {
        $this->assertEquals(Validator::processKeyword($this->keywords['keyToClean'][0]), 'foo, bar');
        $this->assertEquals(Validator::processKeyword($this->keywords['keyToClean'][1]), 'foo');
        $this->assertEquals(Validator::processKeyword($this->keywords['keyToClean'][2]), 'bar foo bar foo');
        $this->assertEquals(Validator::processKeyword($this->keywords['keyToClean'][3]), 'foo bar');
        $this->assertEquals(Validator::processKeyword($this->keywords['keyToClean'][4]), '\s//\+?<>');
        $this->assertEquals(Validator::processKeyword($this->keywords['keyToClean'][5]), "\ + / ? $ '<' \" >");
    }
}

class UrlGeneratorTest extends PHPUnit_Framework_TestCase
{
    protected $settings;

    protected function setUp()
    {
        $engines  = array('google', 'bing', 'yahoo', 'ask');
        $offsets  = array(0, 1, 2, 3, 4, 5, 6);
        $keywords = array(Validator::processKeyword('foobar'),
                          Validator::processKeyword('foo'),
                          Validator::processKeyword('foo baz  ?  \/  <>'),
                          Validator::processKeyword('\n\t/   <a>'));
        $this->settings = array('engines'  => $engines,
                                'offsets'  => $offsets,
                                'keywords' => $keywords);
    }

    public function testUrls()
    {
        $this->assertEquals(Generator::makeUrl($this->settings['engines'][0],
                                               $this->settings['keywords'][0],
                                               $this->settings['offsets'][0]),
                            "http://www.google.com/search?q=foobar&start=0");
        $this->assertEquals(Generator::makeUrl($this->settings['engines'][1],
                                               $this->settings['keywords'][1],
                                               $this->settings['offsets'][1]),
                            "http://www.bing.com/search?q=foo&first=11");
        $this->assertEquals(Generator::makeUrl($this->settings['engines'][2],
                                               $this->settings['keywords'][2],
                                               $this->settings['offsets'][2]),
                            "https://search.yahoo.com/search?p=foo+baz+%3F+%5C%2F+%3C%3E&b=21");
        $this->assertEquals(Generator::makeUrl($this->settings['engines'][3],
                                               $this->settings['keywords'][3],
                                               $this->settings['offsets'][3]),
                            'http://us.ask.com/web?q=%5Cn%5Ct%2F+%3Ca%3E&page=4');
        $this->assertEquals(Generator::makeUrl($this->settings['engines'][0],
                                               $this->settings['keywords'][2],
                                               $this->settings['offsets'][4]),
                            "http://www.google.com/search?q=foo+baz+%3F+%5C%2F+%3C%3E&start=40");
        $this->assertEquals(Generator::makeUrl($this->settings['engines'][1],
                                               $this->settings['keywords'][2],
                                               $this->settings['offsets'][5]),
                            "http://www.bing.com/search?q=foo+baz+%3F+%5C%2F+%3C%3E&first=51");
        $this->assertEquals(Generator::makeUrl($this->settings['engines'][2],
                                               $this->settings['keywords'][3],
                                               $this->settings['offsets'][6]),
                            "https://search.yahoo.com/search?p=%5Cn%5Ct%2F+%3Ca%3E&b=61");
    }
}
