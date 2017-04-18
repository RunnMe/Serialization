<?php

namespace Runn\Serialization\Serializers;

use Runn\Serialization\SerializerInterface;

/**
 * Class PassThru
 * "Empty" serializer
 *
 * @package Runn\Serialization\Serializers
 */
class PassThru
    implements SerializerInterface
{

    /**
     * Serialize method
     * @param mixed $data
     * @return string
     */
    public function encode($data): string
    {
        return (string)$data;
    }

    /**
     * Deserialize method
     * @param string $data
     * @return mixed
     * @throws \Runn\Serialization\DecodeException
     */
    public function decode(string $data)
    {
        return $data;
    }

}