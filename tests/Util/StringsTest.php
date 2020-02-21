<?php

namespace Plasticode\Tests\Util;

use PHPUnit\Framework\TestCase;
use Plasticode\Util\Strings;

final class StringsTest extends TestCase
{
    /**
     * @dataProvider alphaNumProvider
     */
    public function testToAlphaNum(?string $original, ?string $expected) : void
    {
        $this->assertEquals(
            $expected,
            Strings::toAlphaNum($original)
        );
    }

    public function alphaNumProvider()
    {
        return [
            [null, null],
            ['abc_123', 'abc_123'],
            ['aba ba, hey', 'ababahey'],
            ['русский yazyk!', 'русскийyazyk'],
        ];
    }

    /**
     * @dataProvider hashTagsProvider
     */
    public function testHashTags(array $original, string $expected) : void
    {
        $this->assertEquals(
            $expected,
            Strings::hashTags($original)
        );
    }

    public function hashTagsProvider()
    {
        return [
            [['abc', 'def ghi'], '#abc #defghi'],
            [['abc!!', '#def_1-2-3'], '#abc #def_123'],
        ];
    }

    /**
     * @dataProvider normalizeProvider
     */
    public function testNormalize(?string $original, ?string $space, ?string $expected) : void
    {
        $this->assertEquals(
            $expected,
            Strings::normalize($original, $space)
        );
    }

    public function normalizeProvider()
    {
        return [
            [null, null, null],
            ['', null, ''],
            [' aza ZA    uhuhuh ', null, 'aza za uhuhuh'],
            [' aza ZA  --uhuhuh-', '-', 'aza-za-uhuhuh'],
        ];
    }

    /**
     * @dataProvider isUrlProvider
     */
    public function testIsUrl(?string $original, bool $expected) : void
    {
        $this->assertEquals(
            $expected,
            Strings::isUrl($original)
        );
    }

    public function isUrlProvider() : array
    {
        return [
            [null, false],
            ['', false],
            ['/', false],
            ['/url', false],
            ['http://hahaha', true],
            ['https://hhehehehe', true],
        ];
    }

    /**
     * @dataProvider isUrlOrRelativeProvider
     */
    public function testIsUrlOrRelative(?string $original, bool $expected) : void
    {
        $this->assertEquals(
            $expected,
            Strings::isUrlOrRelative($original)
        );
    }

    public function isUrlOrRelativeProvider() : array
    {
        return [
            [null, false],
            ['', false],
            ['/', true],
            ['/url', true],
            ['http://hahaha', true],
            ['https://hhehehehe', true],
        ];
    }

    public function testToUtf8() : void
    {
        $this->assertEquals(
            'somecoolstring',
            Strings::toUtf8('some' . chr(0x97) . 'cool' . chr(27) . 'string')
        );
    }

    /**
     * @dataProvider appendQueryParamProvider
     *
     * @param string $request
     * @param string $name
     * @param mixed $value
     * @param string $expected
     * @return void
     */
    public function testAppendQueryParam(string $request, string $name, $value, string $expected) : void
    {
        $this->assertEquals(
            $expected,
            Strings::appendQueryParam($request, $name, $value)
        );
    }

    public function appendQueryParamProvider() : array
    {
        return [
            [
                'http://site.com/some-page',
                'id',
                123,
                'http://site.com/some-page?id=123'
            ],
            [
                'http://site.com/some-page?id=123',
                'name',
                'test',
                'http://site.com/some-page?id=123&name=test'
            ],
        ];
    }

    /**
     * @dataProvider toSlugProvider
     *
     * @param string $original
     * @param string $expected
     * @return void
     */
    public function testToSlug(string $original, string $expected) : void
    {
        $this->assertEquals(
            $expected,
            Strings::toSlug($original)
        );
    }

    public function toSlugProvider() : array
    {
        return [
            ['Illidan Stormrage', 'illidan-stormrage'],
            ['-soMe--  sLug-123 ', 'some-slug-123'],
            ['крутой slug!', 'slug'],
            ['доброе утро', ''],
        ];
    }
}
