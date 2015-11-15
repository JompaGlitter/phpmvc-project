<?php

namespace Idun\HTMLForm;

/**
 * Form class to add users to database
 *
 */
class FormAddUser extends \Mos\HTMLForm\CForm
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
                'placeholder' => 't.ex. Lyxglidaren',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'about' => [
                'type'        => 'textarea',
                'label'       => 'Kort beskrivning:',
                'placeholder' => 't.ex. Värsta grymma grabben/bruden/hen/djuret helt enkelt!',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'email' => [
                'type'        => 'email',
                'label'       => 'Email:',
                'placeholder' => 'email@example.com',
                'required'    => true,
                'validation'  => ['not_empty', 'email_adress'],
            ],
            'url' => [
                'type'        => 'url',
                'label'       => 'Hemsida:',
                'placeholder' => 'http://www.example.com',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'submit' => [
                'type'      => 'submit',
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
        $this->AddOutput("<p><i>DoSubmit(): Form was submitted. Do stuff (save to database) and return true (success) or false (failed processing form)</i></p>");
        //$this->AddOutput("<p><b>Name: " . $this->Value('name') . "</b></p>");
        //$this->AddOutput("<p><b>Email: " . $this->Value('email') . "</b></p>");
        //$this->AddOutput("<p><b>Phone: " . $this->Value('phone') . "</b></p>");
        
        $this->users = new \Idun\Users\Users();
        $this->users->setDI($this->di);
        
        date_default_timezone_set('Europe/Berlin');
        $now = date('Y-m-d H:i:s');
        
        $save = $this->users->save([
            'username' => $this->Value('username'),
            'about' => $this->Value('about'),
            'email' => $this->Value('email'),
            'homepage' => $this->Value('url'),
            //'password' => password_hash($this->Value('acronym'), PASSWORD_DEFAULT),
            'gravatar' => 'http://www.gravatar.com/avatar/' . md5(strtolower(trim($this->Value('email')))),
            'created' => $now,
            ]);
        return $save ? true : false; 
    }



    /**
     * Callback for submit-button.
     *
     */
    public function callbackSubmitFail()
    {
        $this->AddOutput("<p><i>DoSubmitFail(): Form was submitted but I failed to process/save/validate it</i></p>");
        return false;
    }



    /**
     * Callback What to do if the form was submitted?
     *
     */
    public function callbackSuccess()
    {
        $this->AddOUtput("<p><i>Form was submitted and the callback method returned true.</i></p>");

        //$url = 'users/id/' . $this->Value('id');
        $url = 'users';
        $this->redirectTo($url);

    }



    /**
     * Callback What to do when form could not be processed?
     *
     */
    public function callbackFail()
    {
        $this->AddOutput("<p><i>Form was submitted and the Check() method returned false.</i></p>");
        $this->redirectTo();
    }
} 
