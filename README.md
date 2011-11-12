Événement
=========

Événement is a very simple event dispatching library for PHP 5.3.

It has the same design goals as [Silex](http://silex-project.org) and
[Pimple](http://pimple-project.org), to empower the user while staying concise
and simple.

It is very strongly inspired by the EventEmitter API found in
[node.js](http://nodejs.org). It includes an implementation of
[EventEmitter2](https://github.com/hij1nx/EventEmitter2), that extends
the original EventEmitter.

[![Build Status](https://secure.travis-ci.org/igorw/evenement.png)](http://travis-ci.org/igorw/evenement)

Creating an Emitter
-------------------

```php
<?php
require __DIR__.'/vendor/evenement/src/Evenement/EventEmitter.php';
$emitter = new Evenement\EventEmitter();
```

Adding Listeners
----------------

```php
<?php
$emitter->on('user.create', function (User $user) use ($logger) {
    $logger->log(sprintf("User '%s' was created.", $user->getLogin()));
});
```

Emitting Events
---------------

```php
<?php
$emitter->emit('user.create', array($user));
```
