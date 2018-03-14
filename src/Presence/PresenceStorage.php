<?php

namespace SP\RealTimeBundle\Presence;

use Predis\Client;
use Predis\Collection\Iterator\Keyspace;
use Predis\Transaction\MultiExec;
use Ramsey\Uuid\UuidInterface;

class PresenceStorage
{
    /**
     * @var Client
     */
    private $redisClient;
    /**
     * @var string
     */
    private $cachePrefix;

    public function __construct(Client $redisClient, string $cachePrefix)
    {
        $this->redisClient = $redisClient;
        $this->cachePrefix = $cachePrefix;
    }

    public static function assertChannelStorable(string $channel)
    {
        if (preg_match('/^:|:$|::|\*|[^\w-_:]/', $channel)) {
            throw new \InvalidArgumentException('channel name \''.$channel.'\' is not valid and can\'t be stored');
        }
    }

    private function makeChannelKey(string $channel, string $discriminator)
    {
        self::assertChannelStorable($channel);

        return '{'.$this->cachePrefix.'realtime:'.$channel.'}:'.$discriminator;
    }

    private function makeChannelWildcard(string $channel)
    {
        return $this->makeChannelKey($channel, '*');
    }

    private function makeChannelIdKey(string $channel, UuidInterface $uuid)
    {
        return $this->makeChannelKey($channel, $uuid->toString());
    }

    private function makePresenceTokenKey(PresenceToken $presenceToken)
    {
        return $this->makeChannelIdKey($presenceToken->getChannel(), $presenceToken->getUuid());
    }

    /**
     * Stores a channel presence token.
     *
     * @param PresenceToken $presenceToken
     */
    public function store(PresenceToken $presenceToken)
    {
        $ttl = null;

        if (null !== $presenceToken->getToken()->getExpirationDate()) {
            $now = new \DateTime();
            $ttl = $presenceToken->getToken()->getExpirationDate()->getTimestamp() - $now->getTimestamp();
            if ($ttl < 0) {
                // expiring the token in redis, if the token is already expired
                $this->remove($presenceToken->getChannel(), $presenceToken->getUuid());

                return;
            }
        }

        $key = $this->makePresenceTokenKey($presenceToken);
        $this->redisClient->transaction(function (MultiExec $tx) use ($key, $presenceToken, $ttl) {
            $tx->set($key, json_encode($presenceToken));
            if (null === $ttl) {
                $tx->persist($key);
            } else {
                $tx->expire($key, $ttl);
            }
        });
    }

    /**
     * Check if a channel contains at least one presence.
     *
     * @param string $channel
     *
     * @return bool
     */
    public function channelExists(string $channel)
    {
        $match = $this->makeChannelWildcard($channel);

        foreach (new Keyspace($this->redisClient, $match, 1) as $_) {
            return true;
        }

        return false;
    }

    /**
     * Check for a channel presence token by channel and uuid.
     *
     * @param string        $channel
     * @param UuidInterface $uuid
     *
     * @return bool
     */
    public function channelPresenceExists(string $channel, UuidInterface $uuid)
    {
        $key = $this->makeChannelIdKey($channel, $uuid);

        return $this->redisClient->exists($key);
    }

    /**
     * Remove a token if stored.
     *
     * @param string        $channel
     * @param UuidInterface $uuid
     *
     * @return bool true if removed
     */
    public function remove(string $channel, UuidInterface $uuid)
    {
        $key = $this->makeChannelIdKey($channel, $uuid);

        return $this->redisClient->del([$key]) > 0;
    }
}
