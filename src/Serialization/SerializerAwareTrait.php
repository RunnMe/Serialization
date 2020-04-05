<?php

namespace Runn\Serialization;

/**
 * SerializerAwareInterface simplest implementation
 *
 * Trait SerializerAwareTrait
 * @package Runn\Serialization
 */
trait SerializerAwareTrait
{

    /**
     * @var SerializerInterface|null
     */
    protected $serializer;

    /**
     * @param SerializerInterface|null $serializer
     * @return $this
     */
    public function setSerializer(?SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
        return $this;
    }

    /**
     * @return SerializerInterface|null
     */
    public function getSerializer(): ?SerializerInterface
    {
        return $this->serializer;
    }

}
