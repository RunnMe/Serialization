<?php

namespace Runn\Serialization;

/**
 * Interface SerializerAwareInterface
 * @package Runn\Serialization
 */
interface SerializerAwareInterface
{

    /**
     * @param SerializerInterface|null $serializer
     * @return $this
     */
    public function setSerializer(?SerializerInterface $serializer);

    /**
     * @return SerializerInterface|null
     */
    public function getSerializer(): ?SerializerInterface;

}
