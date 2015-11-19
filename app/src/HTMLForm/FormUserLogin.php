<?php

namespace Idun\HTMLForm;

/**
 * Form class to add users to database
 *
 */
class FormUserLogin extends \Mos\HTMLForm\CForm
{
    use \Anax\DI\TInjectionaware,
        \Anax\MVC\TRedirectHelpers;
    
    
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        parent::__construct([], [
            'username' => [
                'type'        => 'text',
                'label'       => 'Användarnamn:',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'password' => [
                'type'        => 'password',
                'label'       => 'Lösenord',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'submit' => [
                'type'      => 'submit',
                'value'     => 'Logga in',
                'callback'  => [$this, 'callbackSubmit'],
            ],
            'submit-reset' => [
                'type'      => 'reset',
                'value'     => 'Återställ',
            ],
        ]);
    }



    /**
     * Customise the check() method.
     *
     * @param callable $callIfSuccess handler to call if function returns true.
     * @param callable $callIfFail    handler to call if function returns true.
     */
    public function check($callIfSuccess = null, $callIfFail = null)
    {
        return parent::check([$this, 'callbackSuccess'], [$this, 'callbackFail']);
    }



    /**
     * Callback for submit-button.
     *
     */
    public function callbackSubmit()
    {
        
        // Inject database service into DI
        $this->users = new \Idun\Users\Users();
        $this->users->setDI($this->di);
        
        /*
        // Get params from login form
        $form_username = $this->Value('username');
        $form_password = $this->Value('password');
        
        // Hash the form password
        $form_password = password_hash($form_password, PASSWORD_DEFAULT);
        
        // Get params from database
        $user_values = $this->users->query()
                                   ->where("username = ?")
                                   ->execute(array($form_username));
        
        $id = implode(array_column($user_values, 'id'));
        $username = implode(array_column($user_values, 'username'));
        $password = implode(array_column($user_values, 'password'));
        $gravatar = implode(array_column($user_values, 'gravatar'));
        
        // Do the values match?
        if ($user_values && $form_password == $password) {
            
            // Populate session with user data
            $this->session->set('user_id', $id);
            $this->session->set('username', $username);
            $this->session->set('user_gravatar', $gravatar);
            
            return true;
        
        } else {
            
            return false;
        }
        */
        
        $saved = $this->users->query()
                    ->where("username = ?")
                    ->execute(array($this->Value("username")));
        
                if ($saved && password_verify($this->Value("password"), $saved[0]->password)) {
                    $this->users->session->set('username', $saved[0]->username);
                    $this->users->session->set('user_id', $saved[0]->id);
                    $this->users->session->set('user_gravatar', $saved[0]->gravatar);
                    return true;    
                } else {
                    return false;    
                }  
        
    }



    /**
     * Callback for submit-button.
     *
     */
    public function callbackSubmitFail()
    {
        return false;
    }



    /**
     * Callback What to do if the form was submitted?
     *
     */
    public function callbackSuccess()
    {

        //$url = 'users/id/' . $this->Value('id');
        $url = '';
        $this->redirectTo($url);

    }



    /**
     * Callback What to do when form could not be processed?
     *
     */
    public function callbackFail()
    {
        $this->redirectTo();
    }
} 
