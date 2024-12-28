# Événement

Événement is a very simple event dispatching library for PHP.

It has the same design goals as [Silex](https://silex.symfony.com/) and
[Pimple](https://github.com/silexphp/Pimple), to empower the user while staying concise
and simple.

It is very strongly inspired by the [EventEmitter](https://nodejs.org/api/events.html#events_class_eventemitter) API found in
[node.js](http://nodejs.org).

![Continuous Integration](https://github.com/igorw/evenement/workflows/CI/badge.svg)
[![Latest Stable Version](https://poser.pugx.org/evenement/evenement/v)](https://packagist.org/packages/evenement/evenement)
[![Total Downloads](https://poser.pugx.org/evenement/evenement/downloads)](https://packagist.org/packages/evenement/evenement)
[![License](https://poser.pugx.org/evenement/evenement/license)](https://packagist.org/packages/evenement/evenement)

## Fetch

The recommended way to install Événement is [through composer](http://getcomposer.org). By running the following command:

```bash
    $ composer require evenement/evenement
```

## Usage

### Creating an Emitter

```php
<?php
$emitter = new Evenement\EventEmitter();
```

### Adding Listeners

```php
<?php
$emitter->on('user.created', static function (User $user) use ($logger): void {
    $logger->log(sprintf("User '%s' was created.", $user->getLogin()));
});
```

### Removing Listeners

```php
<?php
$emitter->removeListener('user.created', static function (User $user) use ($logger): void {
    $logger->log(sprintf("User '%s' was created.", $user->getLogin()));
});
```

### Emitting Events

```php
<?php
$emitter->emit('user.created', [$user]);
```

Tests
-----
```bash
    $ ./vendor/bin/phpunit
```

License
-------
MIT, see LICENSE.
