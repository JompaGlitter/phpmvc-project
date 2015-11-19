<?php

namespace Idun\HTMLForm;

/**
 * Anax base class for wrapping sessions.
 *
 */
class FormAddAnswer extends \Mos\HTMLForm\CForm
{
    use \Anax\DI\TInjectionaware,
        \Anax\MVC\TRedirectHelpers;


    /**
     * Properties
     *
     */
    private $question_id; // id of the answered question
        

    /**
     * Constructor
     *
     */
    public function __construct($username, $id)
    {
        // Set params
        $this->question_id = $id;
        
        
        parent::__construct([], [
            'question' => [
                'type'        => 'hidden',
                'value'       => $id
            ],
            'username' => [
                'type'        => 'hidden',
                'value'       => $username,
            ],
            'text' => [
                'type'        => 'textarea',
                'label'       => 'Svar:',
                'required'    => true,
                'validation'  => ['not_empty'],
                //'class'       => 'commentContent'
            ],
            'submit' => [
                'type'      => 'submit',
                'value'     => 'Svara',
                'callback'  => [$this, 'callbackSubmit'],
                //'class'     => 'commentButtons'
            ],
            'reset' => [
                'type'      => 'reset',
                'value'     => 'Ångra',
                //'class'     => 'commentButtons'
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

        $this->answers = new \Idun\Answers\Answers();
        $this->answers->setDI($this->di);
        
        date_default_timezone_set('Europe/Berlin');
        $now = date('Y-m-d H:i:s');
        //$ip = $this->request->getServer('REMOTE_ADDR');
        
        $save = $this->answers->save([
            'question_id' => $this->Value('question'),
            'username' => $this->Value('username'),
            'text' => $this->Value('text'),
            'created' => $now,
        ]);

        return $save ? true : false;
    }


    /**
     * Callback What to do if the form was submitted?
     *
     */
    public function callbackSuccess()
    {
        
        $url = 'forum/view-question/' . $this->question_id;
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