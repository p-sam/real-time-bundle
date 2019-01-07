<?php

namespace SP\RealTimeBundle\Presence;

use Predis\Client;
use Predis\Collection\Iterator\Keyspace;
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

    private function makeChannelIdKey(string $channel, UuidInterface $id): PresenceStorageKey
    {
        return PresenceStorageKey::makeChannelIdKey($this->cachePrefix, $channel, $id);
    }

    private function makeChannelLastKey(string $channel): PresenceStorageKey
    {
        return PresenceStorageKey::makeChannelLastKey($this->cachePrefix, $channel);
    }

    /**
     * Stores a channel presence token.
     *
     * @param PresenceToken $presenceToken
     */
    public function store(PresenceToken $presenceToken)
    {
        $now = new \DateTime();
        $ttl = $presenceToken->getToken()->getExpirationDate()->getTimestamp() - $now->getTimestamp();
        if ($ttl < 0) {
            // expiring the token in redis, if the token is already expired
            $this->remove($presenceToken->getChannel(), $presenceToken->getUuid());

            return;
        }

        $key = $this->makeChannelIdKey($presenceToken->getChannel(), $presenceToken->getUuid())->toString();
        $lastKey = $this->makeChannelLastKey($presenceToken->getChannel())->toString();

        // Calling directly multi/exec instead of the transaction abstraction provided by Predis
        // because the latter is not supported using Sentinel replication
        // https://github.com/nrk/predis/issues/404
        $this->redisClient->multi();
        $this->redisClient->set($key, json_encode($presenceToken));
        $this->redisClient->expire($key, $ttl);
        $this->redisClient->set($lastKey, $presenceToken->getUuid()->toString());
        $this->redisClient->expire($lastKey, $ttl);
        $this->redisClient->exec();
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
        $lastKey = $this->makeChannelLastKey($channel)->toString();

        return $this->redisClient->exists($lastKey) > 0;
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
        $key = $this->makeChannelIdKey($channel, $uuid)->toString();

        return $this->redisClient->exists($key) > 0;
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
        $key = $this->makeChannelIdKey($channel, $uuid)->toString();

        return $this->redisClient->del([$key]) > 0;
    }

    /**
     * sync the last ttl and value scanning the tokens in every channel or the specified one.
     *
     * @param null|string $channel
     */
    public function syncChannelLast(?string $channel)
    {
        if (null === $channel) {
            $matchKey = PresenceStorageKey::makeWildcardKey($this->cachePrefix);
        } else {
            $matchKey = PresenceStorageKey::makeChannelWildcardKey($this->cachePrefix, $channel);
        }

        $lastPresences = [];

        foreach (new Keyspace($this->redisClient, $matchKey->toString()) as $key) {
            $presenceKey = PresenceStorageKey::fromKeyString($key);
            if ('last' === $presenceKey->getDiscriminator()) {
                continue;
            }
            $expires = time() + $this->redisClient->ttl($presenceKey->toString());
            if (!isset($lastPresences[$presenceKey->getNamespace()]) || $lastPresences[$presenceKey->getNamespace()]['expires'] < $expires) {
                $lastPresences[$presenceKey->getNamespace()] = [
                    'expires' => $expires,
                    'id' => $presenceKey->getDiscriminator(),
                ];
            }
        }

        foreach ($lastPresences as $channel => $presence) {
            $ttl = $presence['expires'] - time();
            if ($ttl > 0) {
                $lastKey = $this->makeChannelLastKey($channel)->toString();
                $this->redisClient->multi();
                $this->redisClient->set(
                    $lastKey,
                    $presence['id']
                );
                $this->redisClient->expire($lastKey, $ttl);
                $this->redisClient->exec();
            }
        }
    }
}
