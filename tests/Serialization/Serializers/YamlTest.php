<?php

namespace Runn\tests\Serialization\Serializers\Json;

use Runn\Serialization\SerializerInterface;
use Runn\Core\Std;
use Runn\Serialization\Serializers\Yaml;

class YamlTest extends \PHPUnit_Framework_TestCase
{

    public function testInterface()
    {
        $serializer = new Yaml();
        $this->assertInstanceOf(SerializerInterface::class, $serializer);
    }

    /*
     * ----------
     */

    public function testEncodeScalar()
    {
        $serializer = new Yaml();

        $this->assertSame('null', $serializer->encode(null));
        $this->assertSame('true', $serializer->encode(true));
        $this->assertSame('false', $serializer->encode(false));

        $this->assertSame('0', $serializer->encode(0));
        $this->assertSame('42', $serializer->encode(42));
        $this->assertSame('-42', $serializer->encode(-42));

        $this->assertSame('3.14159', $serializer->encode(3.14159));
        $this->assertSame('!!float -1.2E+34', $serializer->encode(-1.2e34));

        $this->assertSame('foobar', $serializer->encode('foobar'));
        $this->assertSame("'foo''bar'", $serializer->encode('foo\'bar'));
    }

    public function testDecodeScalar()
    {
        $serializer = new Yaml();

        $this->assertSame(null, $serializer->decode("null"));
        $this->assertSame(true, $serializer->decode("true"));
        $this->assertSame(false, $serializer->decode("false"));

        $this->assertSame(0, $serializer->decode("0"));
        $this->assertSame(42, $serializer->decode("42"));
        $this->assertSame(-42, $serializer->decode("-42"));

        $this->assertSame(3.14159, $serializer->decode("3.14159"));

        $this->assertSame(-1.2e34, $serializer->decode("-1.2e+34"));
        $this->assertSame(-1.2e34, $serializer->decode("-1.2E+34"));
        $this->assertSame(-1.2e34, $serializer->decode("!!float -1.2E+34"));

        $this->assertSame('foobar', $serializer->decode('foobar'));
        $this->assertSame('foobar', $serializer->decode('"foobar"'));
        $this->assertSame('foobar', $serializer->decode("'foobar'"));

        $this->assertSame('foo\'bar', $serializer->decode('foo\'bar'));
        $this->assertSame('foo\'bar', $serializer->decode('"foo\'bar"'));
        $this->assertSame('foo\'bar', $serializer->decode("'foo''bar'"));
    }

    /*
     * ----------
     */

    public function testEncodeSimpleArray()
    {
        $serializer = new Yaml();
        $n = PHP_EOL;

        $this->assertSame(
            "- foo{$n}- 42{$n}- 3.14159{$n}- true{$n}- false{$n}- null{$n}",
            $serializer->encode(['foo', 42, 3.14159, true, false, null])
        );

        $this->assertSame(
            "1: foo{$n}2: 42{$n}3: 3.14159{$n}4: true{$n}5: false{$n}6: null{$n}",
            $serializer->encode([1 => 'foo', 2 => 42, 3 => 3.14159, 4 => true, 5 => false, 6 => null])
        );

        $this->assertSame(
            "foo: bar{$n}baz: 42{$n}quux: 3.14159{$n}quuux: null{$n}",
            $serializer->encode(['foo' => 'bar', 'baz' => 42, 'quux' => 3.14159, 'quuux' => null])
        );
    }

    public function testDecodeSimpleArray()
    {
        $serializer = new Yaml();
        $n = PHP_EOL;

        $this->assertSame(
            ['foo', 42, 3.14159, true, false, null],
            $serializer->decode('["foo", 42, 3.14159, true, false, null]')
        );
        $this->assertSame(
            ['foo', 42, 3.14159, true, false, null],
            $serializer->decode("- foo{$n}- 42{$n}- 3.14159{$n}- true{$n}- false{$n}- null{$n}")
        );

        $this->assertSame(
            [1 => 'foo', 2 => 42, 3 => 3.14159, 4 => true, 5 => false, 6 => null],
            $serializer->decode('{"1":"foo","2":42,"3":3.14159,"4":true,"5":false,"6":null}')
        );
        $this->assertSame(
            [1 => 'foo', 2 => 42, 3 => 3.14159, 4 => true, 5 => false, 6 => null],
            $serializer->decode("1: foo{$n}2: 42{$n}3: 3.14159{$n}4: true{$n}5: false{$n}6: null{$n}")
        );

        $this->assertSame(
            ['foo' => 'bar', 'baz' => 42, 'quux' => 3.14159, 'quuux' => null],
            $serializer->decode('{"foo":"bar","baz":42,"quux":3.14159,"quuux":null}')
        );
        $this->assertSame(
            ['foo' => 'bar', 'baz' => 42, 'quux' => 3.14159, 'quuux' => null],
            $serializer->decode("foo: bar{$n}baz: 42{$n}quux: 3.14159{$n}quuux: null{$n}")
        );
    }

    /*
     * ----------
     */

    public function testEncodeNestedArray()
    {
        $serializer = new Yaml();
        $n = PHP_EOL;

        $this->assertSame(
            "- 1{$n}- 2{$n}-{$n}    - 3{$n}    - 4{$n}",
            $serializer->encode([1, 2, [3, 4]])
        );
    }

    public function testDecodeNestedArray()
    {
        $serializer = new Yaml();
        $n = PHP_EOL;

        $this->assertSame(
            [1, 2, [3, 4]],
            $serializer->decode('[1,2,[3,4]]')
        );

        $this->assertSame(
            [1, 2, [3, 4]],
            $serializer->decode("- 1{$n}- 2{$n}-{$n}    - 3{$n}    - 4{$n}")
        );

    }

    /*
     * ----------
     */

    public function testEncodeSimpleObject()
    {
        $serializer = new Yaml();
        $obj = new \stdClass();
        $obj->foo = 'bar';
        $obj->baz = 42;

        $this->assertSame(
            '!php/object:O:8:"stdClass":2:{s:3:"foo";s:3:"bar";s:3:"baz";i:42;}',
            $serializer->encode($obj)
        );
    }

    public function testDecodeSimpleObject()
    {
        $serializer = new Yaml();
        $obj = new \stdClass();
        $obj->foo = 'bar';
        $obj->baz = 42;
        $serializedObj = $serializer->encode($obj);

        $this->assertEquals($obj, $serializer->decode($serializedObj));
    }

    /*
     * ----------
     */

    public function testEncodeStdObject()
    {
        $serializer = new Yaml();
        $obj = new Std();
        $obj->foo = 'bar';
        $obj->baz = 42;

        $this->assertSame(
            '!php/object:C:13:"Runn\Core\Std":41:{a:2:{s:3:"foo";s:3:"bar";s:3:"baz";i:42;}}',
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
        $serializer = new Yaml();
        $serializer->encode(fopen(__FILE__, 'r'), 2, 4, \Symfony\Component\Yaml\Yaml::DUMP_EXCEPTION_ON_INVALID_TYPE);
        $this->fail();
    }

    /**
    * @expectedException \Runn\Serialization\DecodeException
    **/
    public function testJsonDecodeError()
    {
        $serializer = new Yaml();
        $serializer->decode("@@@");
        $this->fail();
    }
}
