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
        // @7.3 use JSON_THROW_ON_ERROR
        $encoded = json_encode($data, $options, $depth);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new EncodeException(json_last_error_msg());
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
        // @7.3 use JSON_THROW_ON_ERROR
        $decoded = json_decode($data, $assoc, $depth, $options);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new DecodeException(json_last_error_msg());
        }

        return $decoded;
    }

}
