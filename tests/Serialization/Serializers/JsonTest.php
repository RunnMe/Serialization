<?php

namespace Runn\tests\Serialization\Serializers\Json;

use Runn\Serialization\SerializerInterface;
use Runn\Serialization\Serializers\Json;
use Runn\Core\Std;

class JsonTest extends \PHPUnit_Framework_TestCase
{

    public function testInterface()
    {
        $serializer = new Json();
        $this->assertInstanceOf(SerializerInterface::class, $serializer);
    }

    /*
     * ----------
     */

    public function testEncodeScalar()
    {
        $serializer = new Json();

        $this->assertSame('null', $serializer->encode(null));
        $this->assertSame('true', $serializer->encode(true));
        $this->assertSame('false', $serializer->encode(false));

        $this->assertSame('0', $serializer->encode(0));
        $this->assertSame('42', $serializer->encode(42));
        $this->assertSame('-42', $serializer->encode(-42));

        $this->assertSame('3.14159', $serializer->encode(3.14159));
        $this->assertSame('-1.2e+34', $serializer->encode(-1.2e34));

        $this->assertSame('"foobar"', $serializer->encode('foobar'));
        $this->assertSame('"foo\'bar"', $serializer->encode('foo\'bar'));
    }

    public function testDecodeScalar()
    {
        $serializer = new Json();

        $this->assertSame(null, $serializer->decode("null"));
        $this->assertSame(true, $serializer->decode("true"));
        $this->assertSame(false, $serializer->decode("false"));

        $this->assertSame(0, $serializer->decode("0"));
        $this->assertSame(42, $serializer->decode("42"));
        $this->assertSame(-42, $serializer->decode("-42"));

        $this->assertSame(3.14159, $serializer->decode("3.14159"));
        $this->assertSame(-1.2e34, $serializer->decode("-1.2e+34"));

        $this->assertSame('foobar', $serializer->decode('"foobar"'));
        $this->assertSame('foo\'bar', $serializer->decode('"foo\'bar"'));
    }

    /*
     * ----------
     */

    public function testEncodeSimpleArray()
    {
        $serializer = new Json();

        $this->assertSame(
            '["foo",42,3.14159,true,false,null]',
            $serializer->encode(['foo', 42, 3.14159, true, false, null])
        );

        $this->assertSame(
            '{"1":"foo","2":42,"3":3.14159,"4":true,"5":false,"6":null}',
            $serializer->encode([1 => 'foo', 2 => 42, 3 => 3.14159, 4 => true, 5 => false, 6 => null])
        );

        $this->assertSame(
            '{"foo":"bar","baz":42,"quux":3.14159,"quuux":null}',
            $serializer->encode(['foo' => 'bar', 'baz' => 42, 'quux' => 3.14159, 'quuux' => null])
        );
    }

    public function testDecodeSimpleArray()
    {
        $serializer = new Json();

        $this->assertSame(
            ['foo', 42, 3.14159, true, false, null],
            $serializer->decode('["foo", 42, 3.14159, true, false, null]')
        );

        $this->assertSame(
            [1 => 'foo', 2 => 42, 3 => 3.14159, 4 => true, 5 => false, 6 => null],
            $serializer->decode('{"1":"foo","2":42,"3":3.14159,"4":true,"5":false,"6":null}', true)
        );

        $this->assertSame(
            ['foo' => 'bar', 'baz' => 42, 'quux' => 3.14159, 'quuux' => null],
            $serializer->decode('{"foo":"bar","baz":42,"quux":3.14159,"quuux":null}', true)
        );
    }

    /*
     * ----------
     */

    public function testEncodeNestedArray()
    {
        $serializer = new Json();

        $this->assertSame(
            '[1,2,[3,4]]',
            $serializer->encode([1, 2, [3, 4]])
        );
    }

    public function testDecodeNestedArray()
    {
        $serializer = new Json();

        $this->assertSame(
            [1, 2, [3, 4]],
            $serializer->decode('[1,2,[3,4]]')
        );
    }

    /*
     * ----------
     */

    public function testEncodeSimpleObject()
    {
        $serializer = new Json();
        $obj = new \stdClass();
        $obj->foo = 'bar';
        $obj->baz = 42;

        $this->assertSame(
            '{"foo":"bar","baz":42}',
            $serializer->encode($obj)
        );
    }

    public function testDecodeSimpleObject()
    {
        $serializer = new Json();
        $obj = new \stdClass();
        $obj->foo = 'bar';
        $obj->baz = 42;
        $serializedObj = $serializer->encode($obj);

        $this->assertEquals(['foo' => 'bar', 'baz' => 42], $serializer->decode($serializedObj));
        $this->assertEquals($obj, $serializer->decode($serializedObj, false));
    }

    /*
     * ----------
     */

    public function testEncodeStdObject()
    {
        $serializer = new Json();
        $obj = new Std();
        $obj->foo = 'bar';
        $obj->baz = 42;

        $this->assertSame(
            '{"foo":"bar","baz":42}',
            $serializer->encode($obj)
        );
    }

    /*
     * ----------
     */

    /**
    * @expectedException \Runn\Serialization\EncodeException
    **/
    public function testJsonEncodeError()
    {
        $serializer = new Json();
        $serializer->encode(NAN);
        $this->fail();
    }

    /**
    * @expectedException \Runn\Serialization\DecodeException
    **/
    public function testJsonDecodeError()
    {
        $serializer = new Json();
        $serializer->decode("{'invalid':'data'}");
        $this->fail();
    }
}