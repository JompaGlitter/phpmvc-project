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
         * Properties
         */
        private $user_id; // id of the user
        


        /**
         * Constructor
         *
         */
        public function __construct($id, $username, $about, $email, $url)
        {
            // Set params
            $this->user_id = $id;
            
            
            // Construct the form
            parent::__construct([], [
                'id' => [
                    'type'  => 'hidden',
                    'value'       => $id,
                    'required'    => true,
                    'validation'  => ['not_empty'],
                ],
                'about' => [
                    'type'        => 'textarea',
                    'label'       => 'Kort beskrivning:',
                    'value'       => $about,
                    'required'    => true,
                    'validation'  => ['not_empty'],
                ],
                'email' => [
                    'type'        => 'email',
                    'label'       => 'Email:',
                    'value'       => $email,
                    'required'    => true,
                    'validation'  => ['not_empty', 'email_adress'],
                ],
                'url' => [
                    'type'        => 'url',
                    'label'       => 'Hemsida:',
                    'value'       => $url,
                    'required'    => true,
                    'validation'  => ['not_empty'],
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

            $this->users = new \Idun\Users\Users();
            $this->users->setDI($this->di);
            
            date_default_timezone_set('Europe/Berlin');
            $now = date('Y-m-d H:i:s');
            
            $save = $this->users->save([
                'id'  => $this->Value('id'),
                'about' => strip_tags($this->Value('about')),
                'email' => strip_tags($this->Value('email')),
                'homepage' => strip_tags($this->Value('url')),
                //'password' => password_hash($this->Value('acronym'), PASSWORD_DEFAULT),
                'gravatar' => 'http://www.gravatar.com/avatar/' . md5(strtolower(trim($this->Value('email')))),
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
            return false;
        }



        /**
         * Callback What to do if the form was submitted?
         *
         */
        public function callbackSuccess()
        {
            $url = 'users/id/' . $this->user_id;
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