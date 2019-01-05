<?php

namespace Runn\Serialization\Serializers;

use Runn\Serialization\EncodeException;
use Runn\Serialization\DecodeException;
use Runn\Serialization\SerializerInterface;

/**
 * Class Json
 * @package Runn\Serialization\Serializers
 */
class Json
    implements SerializerInterface
{

    /**
     * Serialize method
     * @param mixed $data
     * @param int $options
     * @param int $depth
     * @return string
     * @throws \Runn\Serialization\EncodeException
     */
    public function encode($data, int $options = 0, int $depth = 512): string
    {
        try {
            $encoded = json_encode($data, $options | JSON_THROW_ON_ERROR, $depth);
        } catch (\JsonException $e) {
            throw new EncodeException($e->getMessage(), $e->getCode(), $e);
        }
        return $encoded;
    }

    /**
     * Deserialize method
     * @param string $data
     * @param bool $assoc
     * @param int $depth
     * @param int $options
     * @return mixed
     * @throws \Runn\Serialization\DecodeException
     */
    public function decode(string $data, bool $assoc = true, int $depth = 512, int $options = 0)
    {
        try {
            $decoded = json_decode($data, $assoc, $depth, $options | JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new DecodeException($e->getMessage(), $e->getCode(), $e);
        }
        return $decoded;
    }

}
