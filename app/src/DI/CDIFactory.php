<?php

namespace Idun\DI;

/**
 * New base class for the Idun framework and extension of 
 * Anax base class which implements Dependency Injection / Service Locator 
 * of the services used by the framework, using lazy loading.
 *
 */
class CDIFactory extends \Anax\DI\CDIFactoryDefault
{
    public function __construct()
    {
        parent::__construct();
        
        
        // Inject the comment service into the app
        $this->set('CommentController', function() {
            $controller = new \Idun\Comment\CommentController();
            $controller->setDI($this);
            return $controller;
        });
        
        
        // Inject the form service into the app
        $this->set('form', '\Mos\HTMLForm\CForm');
        
        
        // Inject the form controller into the app
        $this->set('FormController', function () {
            $controller = new \Anax\HTMLForm\FormController();
            $controller->setDI($this);
            return $controller;
        });
        
        // Inject the database service into the app
        $this->setShared('db', function() {
            $db = new \Mos\Database\CDatabaseBasic();
            $db->setOptions(require ANAX_APP_PATH . 'config/database_mysql.php');
            $db->connect();
            return $db;
        });
        
        // Inject the user and admin controller into the app
        $this->set('UsersController', function() {
            $controller = new \Idun\Users\UsersController();
            $controller->setDI($this);
            return $controller;
        });
        
        // Inject the forum controller into the app
        $this->set('ForumController', function () {
            $controller = new \Idun\Forum\ForumController();
            $controller->setDI($this);
            return $controller;
        });
        
        // Inject the forum controller into the app
        $this->set('sessionmodel', function () {
            $sessionmodel = new \Idun\Session\CSessionModel();
            $sessionmodel->setDI($this);
            return $sessionmodel;
        });
        
    }
}