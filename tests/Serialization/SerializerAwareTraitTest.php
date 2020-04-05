<?php

namespace Runn\tests\Serialization\SerializerAwareTrait;

use PHPUnit\Framework\TestCase;
use Runn\Serialization\SerializerAwareInterface;
use Runn\Serialization\SerializerAwareTrait;
use Runn\Serialization\Serializers\PassThru;

class SerializerAwareTraitTest extends TestCase
{

    public function testTrait()
    {
        $obj = new class implements SerializerAwareInterface { use SerializerAwareTrait; };

        $this->assertNull($obj->getSerializer());

        $serializer = new PassThru();

        $ret = $obj->setSerializer($serializer);
        $this->assertSame($serializer, $obj->getSerializer());
        $this->assertSame($obj, $ret);

        $ret = $obj->setSerializer(null);
        $this->assertNull($obj->getSerializer());
        $this->assertSame($obj, $ret);
    }

}
