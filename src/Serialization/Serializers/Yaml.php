<?php

namespace Runn\Serialization\Serializers;

use Runn\Serialization\EncodeException;
use Runn\Serialization\DecodeException;
use Runn\Serialization\SerializerInterface;

/**
 * Class Yaml
 * @package Runn\Serialization\Serializers
 */
class Yaml
    implements SerializerInterface
{

    /**
     * Serialize method
     * @param mixed $data
     * @param int $inline
     * @param int $indent
     * @param int $flags
     * @return string
     * @throws \Runn\Serialization\EncodeException
     */
    public function encode($data, $inline = 2, $indent = 4, $flags = 0): string
    {
        try {
            $flags |= \Symfony\Component\Yaml\Yaml::DUMP_OBJECT;
            return \Symfony\Component\Yaml\Yaml::dump($data, $inline, $indent, $flags);
        } catch (\Throwable $e) {
            throw new EncodeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Deserialize method
     * @param string $data
     * @param int $flags
     * @return mixed
     * @throws \Runn\Serialization\DecodeException
     */
    public function decode(string $data, $flags = 0)
    {
        try {
            $flags |= \Symfony\Component\Yaml\Yaml::PARSE_OBJECT;
            return \Symfony\Component\Yaml\Yaml::parse($data, $flags);
        } catch (\Throwable $e) {
            throw new DecodeException($e->getMessage(), $e->getCode(), $e);
        }
    }

}
