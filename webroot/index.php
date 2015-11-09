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




// Home rout, the 'me' page
$app->router->add('', function() use ($app) {
    
    $app->theme->addStylesheet('css/form.css');
    $app->theme->addStylesheet('css/comment.css');
    $app->theme->setTitle("Om mig");
    
    $content = $app->fileContent->get('WGTOTW.md');
    $content = $app->textFilter->doFilter($content, 'shortcode, markdown');

    // View for main and byline content
    $app->views->add('default/article', [
        'content' => $content
    ]);

});



// Rout to SmallTalk, the discussion page
$app->router->add('questions', function() use ($app) {

    $app->theme->addStylesheet('css/form.css');
    $app->theme->addStylesheet('css/comment.css');
    $app->theme->setTitle("Frågor");
    
    // Prepare the page content.
    $app->theme->setVariable('main', "
                   <h1>Frågor</h1>
                   <p>Här visas en lista med alla frågor.</p>
                   ");
    
    // Dispatcher for all comments view
    $app->dispatcher->forward([
        'controller'    => 'forum',
        'action'        => 'view-all-questions'
    ]);
    
});



// Rout to SmallTalk, the discussion page
$app->router->add('tags', function() use ($app) {

    $app->theme->setTitle("Taggar");
    
    // Prepare the page content.
    $app->theme->setVariable('main', "
                   <h1>Taggar</h1>
                   <p>Här visas alla taggar som en fråga kan ha.</p>
                   ");
    
    // Dispatcher for all tags view
    $app->dispatcher->forward([
        'controller'    => 'forum',
        'action'        => 'view-all-tags'
    ]);
    
});



// Router for viewing and editing users
$app->router->add('users', function () use ($app) {

    $app->theme->setTitle("Användare");

    // Dispatcher to show all users
    $app->dispatcher->forward([
        'controller' => 'users',
        'action'     => 'view-all-users',
    ]);

});



// Rout to SmallTalk, the discussion page
$app->router->add('ask', function() use ($app) {

    $app->theme->setTitle("Ställ ny fråga");
    
    // Prepare the page content.
    $app->theme->setVariable('main', "
                   <h1>Ställ ny fråga</h1>
                   <p>Snart kan du ställa nya frågor på sidan.</p>
                   ");
    
});



// Rout to show source code
$app->router->add('source', function() use ($app) {
    
    $app->theme->addStylesheet('css/source.css');
    $app->theme->setTitle("Källkod");
    
    $source = new \Mos\Source\CSource([
        'secure_dir' => '..',
        'base_dir' => '..',
        'add_ignore' => [], // ['.htaccess']
    ]);
    
    $app->views->add('source/source', [
       'content' => $source->View(), 
    ]);

});




// Check for matching routes and dispatch to controller/handler of route
$app->router->handle();

// Leave the rest to the rendering phase
$app->theme->render();