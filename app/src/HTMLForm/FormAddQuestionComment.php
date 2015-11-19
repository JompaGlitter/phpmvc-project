<?php

namespace Idun\HTMLForm;

/**
 * Anax base class for wrapping sessions.
 *
 */
class FormAddQuestionComment extends \Mos\HTMLForm\CForm
{
    use \Anax\DI\TInjectionaware,
        \Anax\MVC\TRedirectHelpers;


    /**
     * Properties
     */
    private $question_id; // id of the answered question. For redirection purpose.
    


    /**
     * Constructor
     * 
     * @param $username, name of the user.
     * @param $id, id of the answered question.
     *
     */
    public function __construct($username, $id)
    {
        // Set params
        $this->question_id = $id;
        
        
        // Construct the form
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

        $this->comments = new \Idun\Comments\Comments();
        $this->comments->setDI($this->di);
        
        date_default_timezone_set('Europe/Berlin');
        $now = date('Y-m-d H:i:s');
        //$ip = $this->request->getServer('REMOTE_ADDR');
        
        $save = $this->comments->save([
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