p-sam/real-time-bundle
=============================

[![license-badge]][license] [![release-version-badge]][packagist] ![php-version-badge]

Real-time events from server to browsers and mobile devices via "channels"

_Please note that this bundle makes uses of [ably.io](https://www.ably.io/) and [Redis](https://redis.io/) via [Predis](https://github.com/nrk/predis)._

## Installation ##

Install with composer:

```
$ composer require p-sam/real-time-bundle
```

Then register the bundle in the `AppKernel.php` file:

```php
public function registerBundles()
{
    $bundles = array(
        // ...
        new SP\RealTimeBundle\SPRealTimeBundle(),
        // ...
    );

    return $bundles;
}
```

Then add the following to your `routing.yml`:

```yml
sp_realtime:
    resource: "@SPRealTimeBundle/Controller/"
    type: annotation
```

**Note**: Registering the bundle and the routes are done automatically if you're using Symfony Flex

## Configuration ##

Configure the `predis` client and `ably` key in your `config.yml`:

```yml
sp_realtime:
    ably:
        api_key: '- ably key here -'
        ttl: 3600 # in seconds
    redis:
        key_prefix: 'app:'
    presence_check: true
```

## Usage ##

### From PHP ###

Services are provided from the bundle:

* `sp_real_time.sender`: Allows sending of messages to channels
* `sp_real_time.presence`: Allow subscribing by providing tokens, and checking for presence in channels

The following events are dispatched:
* `sp_real_time.event.subscribe`: A `SubscribeEvent` is emitted when a token is generated for a channel
* `sp_real_time.event.message`: A `MessageEvent` is emitted when a message is sent upstream to ably.io


<!-- Badges -->
[packagist]: https://packagist.org/packages/sperrichon/real-time-bundle
[license]: LICENSE
[license-badge]: https://img.shields.io/github/license/sperrichon/real-time-bundle.svg?style=flat-square
[php-version-badge]: https://img.shields.io/packagist/php-v/sperrichon/real-time-bundle.svg?style=flat-square
[release-version-badge]: https://img.shields.io/packagist/v/sperrichon/real-time-bundle.svg?style=flat-square&label=release