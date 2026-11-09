<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/hello-lumen', function () {
    return "<h1>Lumen</h1><p>Hi good developer, thank for using Lumen</p>";
});

$router->get('/hello-lumen/{name}', function ($name) {
    return "<h1>Lumen</h1><p>Hi <b>" . $name . "</b>, thank for using Lumen</p>";
});

$router->get('/scores', ['middleware' => 'login', function () {
    return "<h1>Selamat</h1><p>Nilai anda 100/100</p>";
}]);

$router->get('/', 'UsersController@status');
$router->get('/users', 'UsersController@getAllUsers');
$router->get('/users/{userId}', 'UsersController@getUserById');

$router->get('/posts/published', 'PostsController@getPublishedPosts');


$router->get('/post/{id}', 'PostsController@show');
$router->put('/post/{id}', 'PostsController@update');
$router->delete('/post/{id}', 'PostsController@destroy');

$router->get('/posts/{postId}/comments', 'PostsController@comments');
$router->post('/posts/{postId}/comments', 'PostsController@commentStore');
$router->delete('/posts/{postId}/comments/{commentId}', 'PostsController@commentDestroy');
$router->get('/post/{postId}/comments', 'PostsController@comments');
$router->post('/post/{postId}/comments', 'PostsController@commentStore');
$router->delete('/post/{postId}/comment/{commentId}', 'PostsController@commentDestroy');

$router->group(['middleware' => ['auth']], function () use ($router) {
    $router->get('/books', 'BooksController@index');
    $router->post('/books', 'BooksController@store');
    $router->get('/books/{id}', 'BooksController@show');
    $router->put('/books/{id}', 'BooksController@update');
    $router->delete('/books/{id}', 'BooksController@destroy');
});

// authentication
$router->group(['prefix' => 'auth'], function () use ($router) {
    // Matches "/auth/register
    $router->post('/register', 'AuthController@register');
    $router->post('/login', 'AuthController@login');
});

// Post
$router->group(['middleware' => ['auth']], function () use ($router) {
    $router->get('/posts', 'PostsController@index');
    $router->post('/posts', 'PostsController@store');
    $router->get('/post/{id}', 'PostsController@show');
    $router->put('/post/{id}', 'PostsController@update');
    $router->delete('/post/{id}', 'PostsController@destroy');
});