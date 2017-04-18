<?php

namespace Runn\tests\Serialization\Serializers\PassThru;

use Runn\Serialization\SerializerInterface;
use Runn\Serialization\Serializers\PassThru;

class PassThruTest extends \PHPUnit_Framework_TestCase
{

    public function testInterface()
    {
        $serializer = new PassThru();
        $this->assertInstanceOf(SerializerInterface::class, $serializer);
    }

    /*
     * ----------
     */

    public function testEncodeScalar()
    {
        $serializer = new PassThru();

        $this->assertSame('',  $serializer->encode(null));
        $this->assertSame('1',  $serializer->encode(true));
        $this->assertSame('', $serializer->encode(false));

        $this->assertSame('0',  $serializer->encode(0));
        $this->assertSame('42', $serializer->encode(42));
        $this->assertSame('-42', $serializer->encode(-42));

        $this->assertSame('3.14159', $serializer->encode(3.14159));
        $this->assertSame('-1.2E+34', $serializer->encode(-1.2e34));

        $this->assertSame('foobar', $serializer->encode('foobar'));
        $this->assertSame('foo\'bar', $serializer->encode('foo\'bar'));
    }

    public function testDecodeScalar()
    {
        $serializer = new PassThru();

        $this->assertSame('',  $serializer->decode(''));
        $this->assertSame('null',  $serializer->decode('null'));
        $this->assertSame('true',  $serializer->decode('true'));
        $this->assertSame('false', $serializer->decode('false'));

        $this->assertSame('0',  $serializer->decode('0'));
        $this->assertSame('42', $serializer->decode('42'));
        $this->assertSame('-42', $serializer->decode('-42'));

        $this->assertSame('3.14159', $serializer->decode('3.14159'));
        $this->assertSame('-1.2e34', $serializer->decode('-1.2e34'));

        $this->assertSame('foobar', $serializer->decode('foobar'));
        $this->assertSame('foo\'bar', $serializer->decode('foo\'bar'));
    }

}