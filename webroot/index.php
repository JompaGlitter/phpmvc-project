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




// Route to Index page
$app->router->add('', function() use ($app) {
    
    $app->theme->addStylesheet('css/form.css');
    $app->theme->addStylesheet('css/comment.css');
    $app->theme->setTitle("Välkommen till Allt om Programmering");
    /*
    $content = $app->fileContent->get('WGTOTW.md');
    $content = $app->textFilter->doFilter($content, 'shortcode, markdown');

    // View for main and byline content
    $app->views->add('default/article', [
        'content' => $content
    ]);
    */
    
    $app->dispatcher->forward([
        'controller' => 'forum',
        'action' => 'view-index-page'
    ]);

});



// Route to Questions page
$app->router->add('questions', function() use ($app) {

    $app->theme->addStylesheet('css/form.css');
    $app->theme->addStylesheet('css/comment.css');
    $app->theme->setTitle("Frågor");
    
    // Prepare the page content.
    $app->theme->setVariable('main', "
                   <h2>Frågor</h2>
                   <p>Här visas en lista med alla frågor.</p>
                   ");
    
    // Dispatcher for all comments view
    $app->dispatcher->forward([
        'controller'    => 'forum',
        'action'        => 'view-all-questions'
    ]);
    
});



// Route to Tags page
$app->router->add('tags', function() use ($app) {

    $app->theme->setTitle("Taggar");
    
    // Prepare some page presentation.
    $app->theme->setVariable('main', "
                   <h2>Taggar</h2>
                   <p>Här visas alla taggar som en fråga kan ha.</p>
                   ");
    
    // Dispatcher for all tags view
    $app->dispatcher->forward([
        'controller'    => 'forum',
        'action'        => 'view-all-tags'
    ]);
    
});



// Route to Users page
$app->router->add('users', function () use ($app) {

    $app->theme->setTitle("Användare");
    
    // Prepare some page presentation.
    $app->theme->setVariable('main', "
                   <h2>Användare</h2>
                   <p>Här visas alla registrerade användare på sidan.</p>
                   ");

    // Dispatcher to show all users
    $app->dispatcher->forward([
        'controller' => 'users',
        'action'     => 'view-all-users',
    ]);

});



// Route to Question form
$app->router->add('ask', function() use ($app) {

    $app->theme->setTitle("Skapa ny fråga");
    
    // Prepare some page presentation.
    $app->theme->setVariable('main', "
                   <h2>Skapa ny fråga</h2>
                   <p>Snart kan du ställa nya frågor på sidan.</p>
                   ");
    
});



// Route to Login page
$app->router->add('login', function() use ($app) {

    $app->theme->setTitle('Logga in');
    
    // Prepare some page presentation.
    $app->theme->setVariable('main', "
                   <h2>Logga in</h2>
                   <p>Här kan du snart logga in.</p>
                   ");
    
});



// Route to About page
$app->router->add('about', function() use ($app) {

    $app->theme->setTitle('Om sidan');
    
    // Prepare some page presentation.
    $app->theme->setVariable('main', "
                   <h2>Om sidan</h2>
                   <p>En liten presentation av sidan och mig som skapare kommer snart här.</p>
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