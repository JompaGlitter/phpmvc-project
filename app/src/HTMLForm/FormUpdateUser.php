<?php

namespace Idun\HTMLForm;

/**
 * Form class to update users in database
 *
 */
class FormUpdateUser extends \Mos\HTMLForm\CForm
{
    use \Anax\DI\TInjectionaware,
        \Anax\MVC\TRedirectHelpers;



        /**
         * Constructor
         *
         */
        public function __construct($id, $acronym, $name, $email)
        {
            parent::__construct([], [
                'id' => [
                    'type'  => 'hidden',
                    'value'       => $id,
                    'required'    => true,
                    'validation'  => ['not_empty'],
                ],
                'acronym' => [
                    'type'        => 'text',
                    'label'       => 'Din akronym:',
                    'value'       => $acronym,
                    'required'    => true,
                    'validation'  => ['not_empty'],
                ],
                'name' => [
                    'type'        => 'text',
                    'label'       => 'Ditt namn:',
                    'value'       => $name,
                    'required'    => true,
                    'validation'  => ['not_empty'],
                ],
                'email' => [
                    'type'        => 'text',
                    'value'       => $email,
                    'required'    => true,
                    'validation'  => ['not_empty', 'email_adress'],
                ],
                'submit' => [
                    'type'      => 'submit',
                    'value'     => 'Spara',
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

            $this->users = new \Idun\Users\User();
            $this->users->setDI($this->di);
            
            date_default_timezone_set('Europe/Berlin');
            $now = date('Y-m-d H:i:s');
            
            $save = $this->users->save([
                'id'  => $this->Value('id'),
                'acronym' => $this->Value('acronym'),
                'email' => $this->Value('email'),
                'name' => $this->Value('name'),
                'password' => password_hash($this->Value('acronym'), PASSWORD_DEFAULT),
                'updated' => $now,
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