<?php

namespace Runn\tests\Serialization\Serializers\Php;

use Runn\Core\Std;
use Runn\Serialization\SerializerInterface;
use Runn\Serialization\Serializers\Php;

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

    public static function __set_state($array)
    {
        $testObj = new testClass();
        $testObj->foo = $array['foo'];
        $testObj->setProtectedField($array['baz']);
        $testObj->setPrivateField($array['bar']);
        return $testObj;
    }
}

class FileTest extends \PHPUnit_Framework_TestCase
{

    public function testInterface()
    {
        $serializer = new Php();
        $this->assertInstanceOf(SerializerInterface::class, $serializer);
    }

    /*
     * ----------
     */

    public function testEncodeScalar()
    {
        $serializer = new Php();

        $this->assertSame('NULL',  $serializer->encode(null));
        $this->assertSame('true',  $serializer->encode(true));
        $this->assertSame('false', $serializer->encode(false));

        $this->assertSame('0',  $serializer->encode(0));
        $this->assertSame('42', $serializer->encode(42));
        $this->assertSame('-42', $serializer->encode(-42));

        $this->assertThat(
            $serializer->encode(3.14159),
            $this->logicalOr(
                $this->equalTo('3.14159'),
                $this->equalTo('3.1415899999999999')
            )
        );

        $this->assertThat(
            $serializer->encode(-1.2e34),
            $this->logicalOr(
                $this->equalTo('-1.2E+34'),
                $this->equalTo('1.1999999999999999E+34')
            )
        );

        $this->assertSame("'foobar'", $serializer->encode('foobar'));
        $this->assertSame("'foo\\'bar'", $serializer->encode('foo\'bar'));
    }

    public function testDecodeScalar()
    {
        $serializer = new Php();

        $this->assertSame(null,  $serializer->decode('NULL'));
        $this->assertSame(null,  $serializer->decode('null'));
        $this->assertSame(true,  $serializer->decode('TRUE'));
        $this->assertSame(true,  $serializer->decode('true'));
        $this->assertSame(false, $serializer->decode('FALSE'));
        $this->assertSame(false, $serializer->decode('false'));

        $this->assertSame(0,  $serializer->decode('0'));
        $this->assertSame(42, $serializer->decode('42'));
        $this->assertSame(-42, $serializer->decode('-42'));

        $this->assertSame(3.14159, $serializer->decode('3.14159'));
        $this->assertSame(-1.2e34, $serializer->decode('-1.2E+34'));
        $this->assertSame(-1.2e34, $serializer->decode('-1.2e34'));

        $this->assertSame('foobar', $serializer->decode("'foobar'"));
        $this->assertSame('foo\'bar', $serializer->decode("'foo\\'bar'"));
    }

    /*
     * ----------
     */

    public function testEncodeSimpleArray()
    {
        $serializer = new Php();

        $this->assertSame(
            "[\n  0 => 1,\n  1 => 2,\n  2 => 3,\n]",
            $serializer->encode([1, 2, 3])
        );
        $this->assertSame(
            "[\n  'foo' => 100,\n  'bar' => 200,\n  'baz' => 300,\n]",
            $serializer->encode(['foo' => 100, 'bar' => 200, 'baz' => 300])
        );
    }

    public function testDecodeSimpleArray()
    {
        $serializer = new Php();

        $this->assertSame(
            [1, 2, 3],
            $serializer->decode("[0 => 1, 1 => 2, 2 => 3,]")
        );
        $this->assertSame(
            ['foo' => 100, 'bar' => 200, 'baz' => 300],
            $serializer->decode("['foo' => 100, 'bar' => 200, 'baz' => 300,]")
        );
    }

    /*
     * ----------
     */

    public function testEncodeNestedArray()
    {
        $serializer = new Php();

        $this->assertSame(
            "[\n  0 => 1,\n  1 =>\n  [\n    0 => 2,\n    1 => 3,\n  ],\n]",
            $serializer->encode([1, [2, 3]])
        );
    }

    public function testDecodeNestedArray()
    {
        $serializer = new Php();

        $this->assertSame(
            [1, [2, 3]],
            $serializer->decode("[0 => 1, 1 => [0 => 2, 1 => 3,],]")
        );
    }

    /*
     * ----------
     */

    public function testEncodeSimpleObject()
    {
        $serializer = new Php();
        $obj = new \stdClass();
        $obj->foo = 'bar';
        $obj->baz = 42;

        $this->assertContains(
            $serializer->encode($obj),
            [
                "stdClass::__set_state([\n   'foo' => 'bar',\n   'baz' => 42,\n])",
                // @7.3+
                "(object) array(\n   'foo' => 'bar',\n   'baz' => 42,\n)",
            ]
        );
    }

    public function testEncodeStdObject()
    {
        $serializer = new Php();
        $obj = new Std();
        $obj->foo = 'bar';
        $obj->baz = 42;

        $this->assertSame(
            "Runn\Core\Std::__set_state([\n   '__data' =>\n  [\n    'foo' => 'bar',\n    'baz' => 42,\n  ],\n])",
            $serializer->encode($obj)
        );
    }

    /*
     * ----------
     */

    /**
     * @expectedException \Runn\Serialization\DecodeException
     */
    public function testDecodeParseError()
    {
        $serializer = new Php();
        $serializer->decode('foo() function does not exist');
        $this->fail();
    }

    /*
     * ----------
     */

    public function testEncodeArrayOfSimpleObjects()
    {
        $serializer = new Php();
        $obj1 = new \stdClass();
        $obj1->foo = 'bar';
        $obj1->baz = 42;
        $obj2 = new \stdClass();
        $obj2->bat = 'quux';
        $obj2->quuux = 1337;
        $arrayOfSimpleObjects = [$obj1, $obj2];

        $this->assertSame(
            "[\n  0 =>\n  stdClass::__set_state([\n     'foo' => 'bar',\n     'baz' => 42,\n  ]),\n  1 =>\n  stdClass::__set_state([\n     'bat' => 'quux',\n     'quuux' => 1337,\n  ]),\n]",
            $serializer->encode($arrayOfSimpleObjects)
        );
    }

    public function testEncodeArrayOfStdObjects()
    {
        $serializer = new Php();
        $obj1 = new Std();
        $obj1->foo = 'bar';
        $obj1->baz = 42;
        $obj2 = new Std();
        $obj2->bat = 'quux';
        $obj2->quuux = 1337;
        $arrayOfStdObjects = [$obj1, $obj2];

        $this->assertSame(
            "[\n  0 =>\n  Runn\Core\Std::__set_state([\n     '__data' =>\n    [\n      'foo' => 'bar',\n      'baz' => 42,\n    ],\n  ]),\n  1 =>\n  Runn\Core\Std::__set_state([\n     '__data' =>\n    [\n      'bat' => 'quux',\n      'quuux' => 1337,\n    ],\n  ]),\n]",
            $serializer->encode($arrayOfStdObjects)
        );
    }

    /*
     * ----------
     */

    public function testDecodeObject()
    {
        $serializer = new Php();
        $obj = new testClass();
        $obj->foo = 'quux';
        $obj->setProtectedField('quuux');
        $obj->setPrivateField(42);
        $serializedObj = $serializer->encode($obj);

        $this->assertEquals($obj, $serializer->decode($serializedObj));
    }

}