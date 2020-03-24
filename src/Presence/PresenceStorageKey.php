<?php

namespace SP\RealTimeBundle\Presence;

use Ramsey\Uuid\UuidInterface;

class PresenceStorageKey
{
    /** @var string */
    private $cachePrefix;
    /** @var string */
    private $namespace;
    /** @var string */
    private $discriminator;

    public const ROOT_NAMESPACE = 'realtime';

    /**
     * PresenceStorageKey constructor.
     *
     * @param string $cachePrefix
     * @param string $namespace
     * @param string $discriminator
     */
    private function __construct(string $cachePrefix, string $namespace, string $discriminator)
    {
        $this->cachePrefix = $cachePrefix;
        $this->namespace = $namespace;
        $this->discriminator = $discriminator;
    }

    public static function assertChannelStorable(string $channel)
    {
        if (preg_match('/^:|:$|::|\*|[^\w\-_:]/', $channel)) {
            throw new \InvalidArgumentException('channel name \''.$channel.'\' is not valid and can\'t be stored');
        }
    }

    /**
     * @return string
     */
    public function getCachePrefix(): string
    {
        return $this->cachePrefix;
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @return string
     */
    public function getDiscriminator(): string
    {
        return $this->discriminator;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return '{'.$this->cachePrefix.self::ROOT_NAMESPACE.':'.$this->namespace.'}:'.$this->discriminator;
    }

    private static function makeChannelKey(string $cachePrefix, string $channel, string $discriminator): self
    {
        self::assertChannelStorable($channel);

        return new self($cachePrefix, $channel, $discriminator);
    }

    /**
     * @param string $key
     *
     * @return PresenceStorageKey
     */
    public static function fromKeyString(string $key): self
    {
        $matches = [];
        if (!preg_match('/\{(.*)'.self::ROOT_NAMESPACE.':([^\}]+)\}:(.+)$/', $key, $matches)) {
            throw new \InvalidArgumentException("key '${key}' didn't match format");
        }

        return new self($matches[1], $matches[2], $matches[3]);
    }

    /**
     * Returns the key for one id in a specific channel.
     *
     * @param string        $cachePrefix
     * @param string        $channel
     * @param UuidInterface $uuid
     *
     * @return PresenceStorageKey
     */
    public static function makeChannelIdKey(string $cachePrefix, string $channel, UuidInterface $uuid): self
    {
        return self::makeChannelKey($cachePrefix, $channel, $uuid->toString());
    }

    /**
     * Returns the key where the last id is stored in a specific channel.
     *
     * @param string $cachePrefix
     * @param string $channel
     *
     * @return PresenceStorageKey
     */
    public static function makeChannelLastKey(string $cachePrefix, string $channel): self
    {
        return self::makeChannelKey($cachePrefix, $channel, 'last');
    }

    /**
     *  Returns an expression that matches every key in a specific channel.
     *
     * @param string $cachePrefix
     * @param string $channel
     *
     * @return PresenceStorageKey
     */
    public static function makeChannelWildcardKey(string $cachePrefix, string $channel): self
    {
        return self::makeChannelKey($cachePrefix, $channel, '*');
    }

    /**
     *  Returns an expression that matches every key.
     *
     * @param string $cachePrefix
     *
     * @return PresenceStorageKey
     */
    public static function makeWildcardKey(string $cachePrefix): self
    {
        return new self($cachePrefix, '*', '*');
    }
}
