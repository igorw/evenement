# Événement

Événement is a very simple event dispatching library for PHP 5.3.

It has the same design goals as [Silex](http://silex-project.org) and
[Pimple](http://pimple-project.org), to empower the user while staying concise
and simple.

It is very strongly inspired by the EventEmitter API found in
[node.js](http://nodejs.org).

[![Build Status](https://secure.travis-ci.org/igorw/evenement.png)](http://travis-ci.org/igorw/evenement)

## Fetch

The recommended way to install Événement is [through composer](http://packagist.org).

Just create a composer.json file for your project:

```JSON
{
    "require": {
        "evenement/evenement": "*"
    }
}
```

And run these two commands to install it:

    $ wget http://getcomposer.org/composer.phar
    $ php composer.phar install

Now you can add the autoloader, and you will have access to the library:

```php
<?php
require 'vendor/.composer/autoload.php';
```

## Usage

### Creating an Emitter

```php
<?php
require __DIR__.'/vendor/evenement/src/Evenement/EventEmitter.php';
$emitter = new Evenement\EventEmitter();
```

### Adding Listeners

```php
<?php
$emitter->on('user.create', function (User $user) use ($logger) {
    $logger->log(sprintf("User '%s' was created.", $user->getLogin()));
});
```

### Emitting Events

```php
<?php
$emitter->emit('user.create', array($user));
```

Tests
-----

Before running the tests you need to have composer set up an autoloader:

    $ wget http://getcomposer.org/composer.phar
    $ php composer.phar install

Now you can run the unit tests.

    $ phpunit

License
-------
MIT, see LICENSE.
