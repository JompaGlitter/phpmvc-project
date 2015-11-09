<?php
/**
 * This is a Anax page controller.
 *
 */

// Get environment, autoloader and the $app object.
require __DIR__.'/config_with_app.php';



// Prepare the page content.
$app->theme->setVariable('title', "Hello again - first created page controller in Anax")
           ->setVariable('main', "
               <h1>Hello Again</h1>
               <p>This is my first page controller created in the Anax-MVC environment.</p>
               ");



// Render the response using theme engine.
$app->theme->render();
