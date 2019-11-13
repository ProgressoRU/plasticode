<?php

namespace Plasticode\Tests;

use PHPUnit\Framework\TestCase;
use Plasticode\Util\Strings;

final class StringsTest extends TestCase
{
    /** @dataProvider alphaNumProvider */
    public function testToAlphaNum(?string $original, ?string $expected): void
    {
        $this->assertEquals(
            Strings::toAlphaNum($original),
            $expected
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

    /** @dataProvider hashTagsProvider */
    public function testHashTags(array $original, string $expected): void
    {
        $this->assertEquals(
            Strings::hashTags($original),
            $expected
        );
    }

    public function hashTagsProvider()
    {
        return [
            [['abc', 'def ghi'], '#abc #defghi'],
            [['abc!!', '#def_1-2-3'], '#abc #def_123'],
        ];
    }
}
