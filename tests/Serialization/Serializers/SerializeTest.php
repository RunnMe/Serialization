<?php

namespace Runn\tests\Serialization\Serializers\Serialize;

use PHPUnit\Framework\TestCase;
use Runn\Core\Std;
use Runn\Serialization\DecodeException;
use Runn\Serialization\SerializerInterface;
use Runn\Serialization\Serializers\Serialize;

class testClass
{
    public $foo;
    private $bar;
    protected $baz;

    public function setPrivateField($value)
    {
        $this->bar = $value;
    }

    public function setProtectedField($value)
    {
        $this->baz = $value;
    }
}

class SerializeTest extends TestCase
{

    public function testInterface()
    {
        $serializer = new Serialize();
        $this->assertInstanceOf(SerializerInterface::class, $serializer);
    }

    /*
     * ----------
     */

    public function testEncodeScalar()
    {
        $serializer = new Serialize();

        $this->assertSame('N;',  $serializer->encode(null));
        $this->assertSame('b:1;',  $serializer->encode(true));
        $this->assertSame('b:0;', $serializer->encode(false));

        $this->assertSame('i:0;',  $serializer->encode(0));
        $this->assertSame('i:42;', $serializer->encode(42));
        $this->assertSame('i:-42;', $serializer->encode(-42));

        $this->assertThat(
            $serializer->encode(3.14159),
            $this->logicalOr(
                $this->equalTo('d:3.14159;'),
                $this->equalTo('d:3.1415899999999999;')
            )
        );

        $this->assertThat(
            $serializer->encode(-1.2e34),
            $this->logicalOr(
                $this->equalTo('d:-1.2E+34;'),
                $this->equalTo('d:-1.1999999999999999E+34;')
            )
        );

        $this->assertSame('s:6:"foobar";', $serializer->encode('foobar'));
        $this->assertSame('s:7:"foo\'bar";', $serializer->encode('foo\'bar'));
        $this->assertSame('s:7:"foo"bar";', $serializer->encode('foo"bar'));
    }

    public function testDecodeScalar()
    {
        $serializer = new Serialize();

        $this->assertSame(null,  $serializer->decode('N;'));
        $this->assertSame(true,  $serializer->decode('b:1;'));
        $this->assertSame(false, $serializer->decode('b:0;'));

        $this->assertSame(0,  $serializer->decode('i:0;'));
        $this->assertSame(42, $serializer->decode('i:42;'));
        $this->assertSame(-42, $serializer->decode('i:-42;'));

        $this->assertSame(3.14159, $serializer->decode('d:3.14159;'));
        $this->assertSame(-1.2e34, $serializer->decode('d:-1.2E+34;'));
        $this->assertSame(-1.2e34, $serializer->decode('d:-1.2e34;'));

        $this->assertSame('foobar', $serializer->decode('s:6:"foobar";'));
        $this->assertSame('foo\'bar', $serializer->decode('s:7:"foo\'bar";'));
        $this->assertSame('foo"bar', $serializer->decode('s:7:"foo"bar";'));
    }

    /*
     * ----------
     */

    public function testEncodeSimpleArray()
    {
        $serializer = new Serialize();

        $this->assertSame(
            'a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}',
            $serializer->encode([1, 2, 3])
        );
        $this->assertSame(
            'a:3:{s:3:"foo";i:100;s:3:"bar";i:200;s:3:"baz";i:300;}',
            $serializer->encode(['foo' => 100, 'bar' => 200, 'baz' => 300])
        );
    }

    public function testDecodeSimpleArray()
    {
        $serializer = new Serialize();

        $this->assertSame(
            [1, 2, 3],
            $serializer->decode('a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}')
        );
        $this->assertSame(
            ['foo' => 100, 'bar' => 200, 'baz' => 300],
            $serializer->decode('a:3:{s:3:"foo";i:100;s:3:"bar";i:200;s:3:"baz";i:300;}')
        );
    }

    /*
     * ----------
     */

    public function testEncodeNestedArray()
    {
        $serializer = new Serialize();

        $this->assertSame(
            'a:2:{i:0;i:1;i:1;a:2:{i:0;i:2;i:1;i:3;}}',
            $serializer->encode([1, [2, 3]])
        );
    }

    public function testDecodeNestedArray()
    {
        $serializer = new Serialize();

        $this->assertSame(
            [1, [2, 3]],
            $serializer->decode('a:2:{i:0;i:1;i:1;a:2:{i:0;i:2;i:1;i:3;}}')
        );
    }

    /*
     * ----------
     */

    public function testEncodeSimpleObject()
    {
        $serializer = new Serialize();
        $obj = new \stdClass();
        $obj->foo = 'bar';
        $obj->baz = 42;

        $this->assertSame(
            'O:8:"stdClass":2:{s:3:"foo";s:3:"bar";s:3:"baz";i:42;}',
            $serializer->encode($obj)
        );
    }

    public function testEncodeStdObject()
    {
        $serializer = new Serialize();
        $obj = new Std();
        $obj->foo = 'bar';
        $obj->baz = 42;

        $this->assertSame(
            'C:13:"Runn\Core\Std":41:{a:2:{s:3:"foo";s:3:"bar";s:3:"baz";i:42;}}',
            $serializer->encode($obj)
        );
    }

    /*
     * ----------
     */

    public function testDecodeParseError()
    {
        $serializer = new Serialize();

        $this->expectException(DecodeException::class);
        $serializer->decode('invalid data');
    }

    /*
     * ----------
     */

    public function testEncodeArrayOfSimpleObjects()
    {
        $serializer = new Serialize();
        $obj1 = new \stdClass();
        $obj1->foo = 'bar';
        $obj1->baz = 42;
        $obj2 = new \stdClass();
        $obj2->bat = 'quux';
        $obj2->quuux = 1337;
        $arrayOfSimpleObjects = [$obj1, $obj2];

        $this->assertSame(
            'a:2:{i:0;O:8:"stdClass":2:{s:3:"foo";s:3:"bar";s:3:"baz";i:42;}i:1;O:8:"stdClass":2:{s:3:"bat";s:4:"quux";s:5:"quuux";i:1337;}}',
            $serializer->encode($arrayOfSimpleObjects)
        );
    }

    public function testEncodeArrayOfStdObjects()
    {
        $serializer = new Serialize();
        $obj1 = new Std();
        $obj1->foo = 'bar';
        $obj1->baz = 42;
        $obj2 = new Std();
        $obj2->bat = 'quux';
        $obj2->quuux = 1337;
        $arrayOfStdObjects = [$obj1, $obj2];

        $this->assertSame(
            'a:2:{i:0;C:13:"Runn\Core\Std":41:{a:2:{s:3:"foo";s:3:"bar";s:3:"baz";i:42;}}i:1;C:13:"Runn\Core\Std":46:{a:2:{s:3:"bat";s:4:"quux";s:5:"quuux";i:1337;}}}',
            $serializer->encode($arrayOfStdObjects)
        );
    }

    public function testDecodeObject()
    {
        $serializer = new Serialize();
        $obj = new testClass();
        $obj->foo = 'quux';
        $obj->setProtectedField('quuux');
        $obj->setPrivateField(42);
        $serializedObj = $serializer->encode($obj);

        $this->assertEquals($obj, $serializer->decode($serializedObj));
    }

}
