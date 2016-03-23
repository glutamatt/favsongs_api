<?php

/**
* Prepare your DB and rock on
*/

spl_autoload_register(function($c){
    @include preg_replace('#\\\|_(?!.+\\\)#','/',$c).'.php';
});

use Lib\Application;
use Model\FavoriteSong;
use Model\Song;
use Model\User;

$app = new Application(require 'db.config.php');

$app->get('/users/(\d+)', function ($id) {
    return User::loadById($id) ?: new \Exception('User not found', 404);
});

$app->get('/users/(\d+)/favorites', function ($id) {
    return FavoriteSong::loadByUser($id) ?: new \Exception('Favorites not found', 404);
});

$app->put('/users/(\d+)/favorites/(\d+)', function ($userId, $songId) {
    if (!User::loadById($userId)) return new \Exception('User not found', 404);
    if (!Song::loadById($songId)) return new \Exception('Song not found', 404);
    if (FavoriteSong::create($userId, $songId)) {
        return 201;
    }
    return new \Exception('Unable to add favorite song', 400);
});

$app->delete('/users/(\d+)/favorites/(\d+)', function ($userId, $songId) {
    return FavoriteSong::remove($userId, $songId) ? 204 : 404;
});

$app->get('/songs/(\d+)', function ($id) {
    return Song::loadById($id) ?: new \Exception('Song not found', 404);
});

$app->run();
