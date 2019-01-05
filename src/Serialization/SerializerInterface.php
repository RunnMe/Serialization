<?php

namespace Runn\Serialization;

/**
 * Interface SerializerInterface
 * @package Runn\Serialization
 */
interface SerializerInterface
{

    /**
     * Serialize method
     * @param mixed $data
     * @return string
     */
    public function encode($data) : string;

    /**
     * Deserialize method
     * @param string $data
     * @return mixed
     */
    public function decode(string $data);

}
