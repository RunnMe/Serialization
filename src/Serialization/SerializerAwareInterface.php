<?php

namespace Runn\Serialization;

/**
 * Interface SerializerAwareInterface
 * @package Runn\Serialization
 */
interface SerializerAwareInterface
{

    public function setSerializer(/*?*/SerializerInterface $serializer);

    public function getSerializer(): /*?*/SerializerInterface;

}