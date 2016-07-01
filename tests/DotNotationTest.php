<?php

use \arabcoders\DotNotation\DotNotation;

/**
 * Class DotNotationTest
 */
class DotNotationTest extends \PHPUnit_Framework_TestCase
{
    public function testSet()
    {
        $d = new DotNotation( [ ] );
        $d->set( 'one', 1 );
        $this->assertEquals( [ 'one' => 1 ], $d->getValues() );
    }

    /**
     * @expectedException TypeError
     */
    public function testSetException()
    {
        $d = new DotNotation( [ ] );
        $d->set( [ ], 1 );
    }

    public function testSetOverride()
    {
        $d = new DotNotation( [ 'one' => 1 ] );
        $d->set( 'one', 2 );
        $this->assertEquals( [ 'one' => 2 ], $d->getValues() );
    }

    public function testSetPath()
    {
        $d = new DotNotation( [ 'one' => [ 'two' => 1 ] ] );
        $d->set( 'one.two', 2 );
        $this->assertEquals( [ 'one' => [ 'two' => 2 ] ], $d->getValues() );
    }

    public function testPathAppend()
    {
        $d = new DotNotation( [ 'one' => [ 'two' => 1 ] ] );
        $d->set( 'one.other', 1 );
        $this->assertEquals( [ 'one' => [ 'two' => 1, 'other' => 1 ] ], $d->getValues() );
    }

    public function testSetAppend()
    {
        $d = new DotNotation( [ 'one' => [ 'two' => 1 ] ] );
        $d->set( 'two', 2 );
        $this->assertEquals( [ 'one' => [ 'two' => 1 ], 'two' => 2 ], $d->getValues() );
    }

    public function testSetAppendArray()
    {
        $d = new DotNotation( [ 'one' => [ 'two' => 1 ] ] );
        $d->set( 'one', [ 'two' => 2 ] );
        $this->assertEquals( [ 'one' => [ 'two' => 2 ] ], $d->getValues() );
    }

    public function testSetOverrideAndAppend()
    {
        $d = new DotNotation( [ 'one' => [ 'two' => 1 ] ] );
        $d->set( 'one', [ 'two' => 2, 'other' => 3 ] );
        $this->assertEquals( [ 'one' => [ 'two' => 2, 'other' => 3 ] ], $d->getValues() );
    }

    public function testSetOverrideByArray()
    {
        $d = new DotNotation( [ 'one' => [ 'two' => 1 ] ] );
        $d->set( 'one', [ 'other' => 3 ] );
        $this->assertEquals( [ 'one' => [ 'other' => 3 ] ], $d->getValues() );
    }

    public function testSetPathByDoubleDots()
    {
        $d = new DotNotation( [ 'one' => [ 'two' => [ 'three' => 1 ] ] ] );
        $d->set( 'one:two:three', 3 );
        $this->assertEquals( [ 'one' => [ 'two' => [ 'three' => 3 ] ] ], $d->getValues() );
    }

    public function testGet()
    {
        $d = new DotNotation( [ 'one' => [ 'two' => [ 'three' => 1 ] ] ] );

        $this->assertEquals( null, $d->get( 'foo' ) );

        $this->assertEquals( [ 'two' => [ 'three' => 1 ] ], $d->get( 'one' ) );

        $this->assertEquals( [ 'three' => 1 ], $d->get( 'one.two' ) );

        $this->assertEquals( 1, $d->get( 'one.two.three' ) );

        $this->assertEquals( 'non value', $d->get( 'one.two.three.next', 'non value' ) );
    }

    /**
     * @expectedException TypeError
     */
    public function testExceptionHasErrorcode20()
    {
        $d = new DotNotation( [ 'one' => [ 'two' => [ 'three' => 1 ] ] ] );
        $d->get( null );
    }

    public function testHave()
    {
        $d = new DotNotation( [ 'one' => [ 'two' => [ 'three' => 1 ] ] ] );
        $this->assertTrue( $d->have( 'one' ) );
        $this->assertTrue( $d->have( 'one.two' ) );
        $this->assertTrue( $d->have( 'one.two.three' ) );
        $this->assertFalse( $d->have( 'one.two.three.false' ) );
        $this->assertFalse( $d->have( 'one.false.three' ) );
        $this->assertFalse( $d->have( 'false' ) );
    }
}