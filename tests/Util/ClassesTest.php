<?php

namespace Plasticode\Tests\Util;

use PHPUnit\Framework\TestCase;
use Plasticode\Util\Classes;
use Plasticode\ViewModels\SpoilerViewModel;

/**
 * @covers \Plasticode\Util\Classes
 */
final class ClassesTest extends TestCase
{
    public function testGetPublicMethods() : void
    {
        $this->assertEquals(
            ['id', 'body', 'title', 'toArray'],
            Classes::getPublicMethods(SpoilerViewModel::class)
        );
    }
}
