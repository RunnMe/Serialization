<?php

namespace Runn\Serialization\Serializers;

use Runn\Serialization\DecodeException;
use Runn\Serialization\SerializerInterface;

/**
 * Class Serialize
 * @package Runn\Serialization\Serializers
 */
class Serialize
    implements SerializerInterface
{

    /**
     * Serialize method
     * @param mixed $data
     * @return string
     */
    public function encode($data): string
    {
        return serialize($data);
    }

    /**
     * Deserialize method
     * @param string $data
     * @return mixed
     * @throws \Runn\Serialization\DecodeException
     */
    public function decode(string $data)
    {
        if (serialize(false) == $data) {
            return false;
        } elseif (false !== ($ret = @unserialize($data))) {
            return $ret;
        } else {
            throw new DecodeException('unserialize() error');
        }
    }

}
