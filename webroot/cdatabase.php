<?php
/**
 * This is a Idun front controller for my personal site.
 *
 */

// Get the enviroment, autoloader and the $app object
require __DIR__.'/config_with_app.php';


// Set link creation to 'clean' for a nice, clean link displayment
$app->url->setUrlType(\Anax\Url\CUrl::URL_CLEAN);

// Add site specifik configurations
$app->theme->configure(ANAX_APP_PATH . '/config/theme_me.php');

// Add site specifik navbar
$app->navbar->configure(ANAX_APP_PATH . '/config/navbar_me.php');



// Test form route
$app->router->add('setup', function () use ($app) {
    
    $app->theme->setTitle("Visa alla användare");

    // Display all users in database
    $app->dispatcher->forward([
        'controller' => 'users',
        'action'     => 'setupUsers',
    ]);
 
});



// Router for viewing and editing users
$app->router->add('users', function () use ($app) {

    $app->theme->setTitle("Visa alla användare");

    // Display all users in database
    $app->dispatcher->forward([
        'controller' => 'users',
        'action'     => 'list',
    ]);

});



// Check for matching routes and dispatch to controller/handler of route
$app->router->handle();

// Leave the rest to the rendering phase
$app->theme->render();