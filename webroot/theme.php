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
$app->theme->configure(ANAX_APP_PATH . '/config/theme-grid.php');

// Add site specifik navbar
$app->navbar->configure(ANAX_APP_PATH . '/config/navbar_me.php');



// Home route, the new grid theme page
$app->router->add('', function() use ($app) {
    
    $app->theme->setTitle("Ny tema-sida");
    
    // Get view content and store in variables.
    $main = $app->fileContent->get('theme/main.html');
    $main = $app->textFilter->doFilter($main, 'shortcode, markdown');
    $sidebar = $app->fileContent->get('theme/sidebar.md');
    $footerCol = $app->fileContent->get('theme/footer-col.md');
    
    
    // Create views with attached content
    $app->views->add('default/article', ['content' => $main], 'main')
               ->add('default/article', ['content' => $sidebar], 'sidebar')
 
                ->addString('<i class="fa fa-clock-o fa-3x"></i> <br>Tar ingen tid att fixa!', 'featured-1')
                ->addString('<i class="fa fa-internet-explorer fa-3x"></i> <br>Fungerar, även i Internet Explorer!', 'featured-2')
                ->addString('<i class="fa fa-thumbs-o-up fa-3x"></i> <br>It\'s goot shit!', 'featured-3')
                ->add('default/article', ['content' => $footerCol], 'footer-col-1')
                ->add('default/article', ['content' => $footerCol], 'footer-col-2')
                ->add('default/article', ['content' => $footerCol], 'footer-col-3')
                ->add('default/article', ['content' => $footerCol], 'footer-col-4');
});


// Route to show regions
$app->router->add('regioner', function() use ($app) {
    
    $app->theme->addStylesheet('css/anax-grid/theme-regions-demo.css');
    $app->theme->setTitle("Regioner");
    
    
    $app->views->add('default/article', [
        'content' => null,
    ]);
    $app->views->addString('flash', 'flash')
               ->addString('featured-1', 'featured-1')
               ->addString('featured-2', 'featured-2')
               ->addString('featured-3', 'featured-3')
               ->addString('main', 'main')
               ->addString('sidebar', 'sidebar')
               ->addString('triptych-1', 'triptych-1')
               ->addString('triptych-2', 'triptych-2')
               ->addString('triptych-3', 'triptych-3')
               ->addString('footer-col-1', 'footer-col-1')
               ->addString('footer-col-2', 'footer-col-2')
               ->addString('footer-col-3', 'footer-col-3')
               ->addString('footer-col-4', 'footer-col-4');

});


// Route for typography
$app->router->add('typografi', function() use ($app) {

    $app->theme->setTitle("Typografi");
    
    $content = $app->fileContent->get('typography.html');
    
    $app->views->add('default/article', [
        'content' => $content, 
    ]);
    
    $app->views->add('default/article', [
        'content' => $content,
    ], 'sidebar');

});


// Route for Font Awesome
$app->router->add('fontawesome', function() use ($app) {
    
    $app->theme->setTitle("Font Awesome");
    
    $contentMain = $app->fileContent->get('fa-main.html');
    $contentSidebar = $app->fileContent->get('fa-sidebar.html');
    
    $app->views->add('default/article', [
        'content' => $contentMain,
    ]);
    
    $app->views->add('default/article', [
        'content' => $contentSidebar,
    ], 'sidebar');
    
});



// Check for matching routes and dispatch to controller/handler of route
$app->router->handle();

// Leave the rest to the rendering phase
$app->theme->render();