Événement
=========

Événement is a very simple event dispatching library for PHP 5.3.

It has the same design goals as [Silex](http://silex-project.org) and
[Pimple](http://pimple-project.org), to empower the user while staying concise
and simple.

It is very strongly inspired by the EventEmitter API found in
[node.js](http://nodejs.org).

Creating an Emitter
-------------------

    require __DIR__.'/vendor/evenement/src/Evenement/EventEmitter.php';
    $emitter = new Evenement\EventEmitter();

Adding Listeners
----------------

    $emitter->on('user.create', function (User $user) use ($logger) {
        $logger->log(sprintf("User '%s' was created.", $user->getLogin()));
    });

Emitting Events
---------------

    $emitter->emit('user.create', array($user));
