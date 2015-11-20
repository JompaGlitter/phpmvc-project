<?php

namespace Idun\HTMLForm;

/**
 * Anax base class for wrapping sessions.
 *
 */
class FormAddQuestion extends \Mos\HTMLForm\CForm
{
    use \Anax\DI\TInjectionaware,
        \Anax\MVC\TRedirectHelpers;


    
    /**
     * Properties
     */
    private $dbTags; // Existing tags in database 
    
    
    /**
     * Constructor
     *
     */
    public function __construct($username, $dbTags)
    {
        
        //Set params
        $this->dbTags = $dbTags;
            
        // Construct the form
        parent::__construct([], [
            'username' => [
                'type'        => 'hidden',
                'value'       => $username,
            ],
            'title' => [
                'type'        => 'text',
                'label'       => 'Titel:',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'text' => [
                'type'        => 'textarea',
                'label'       => 'Lägg till en kommentar:',
                'required'    => true,
                'validation'  => ['not_empty'],
                //'class'       => 'commentContent'
            ],
            'tags' => [
                'type'        => 'text',
                'label'       => 'Taggar (OBS! Separera med komma-tecken likt exempel-koden):',
                'required'    => false,
                'placeholder' => 'php, javascript, turbopascal etc.',
                //'class'       => 'commentContent'
            ],
            'submit' => [
                'type'      => 'submit',
                'value'     => 'Skapa',
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
        //
        // Save question in database
        //
        
        $this->questions = new \Idun\Questions\Questions();
        $this->questions->setDI($this->di);
        
        date_default_timezone_set('Europe/Berlin');
        $now = date('Y-m-d H:i:s');
        //$ip = $this->request->getServer('REMOTE_ADDR');
        
        $save = $this->questions->save([
            'username' => $this->Value('username'),
            'title' => $this->Value('title'),
            'text' => $this->Value('text'),
            'created' => $now,
        ]);
        
        
        //
        // Save new tags in database
        //
        
        $this->taggar = new \Idun\Tags\Tags();
        $this->taggar->setDI($this->di);
        
        // Create array of tags from form input
        //$form_tags = explode(',', trim($this->Value('tags')));
        $form_tags = explode(',', str_replace(' ', '', strtolower($this->Value('tags'))));
//echo "<pre>" . var_dump($form_tags) . "</pre>";
        
        // Create array of existing tags supplied by the construct method
        foreach ($this->dbTags as $tag) {
            $existing_tags[] = $tag->tag;
        }

        // Do all tags exist in database?
        foreach ($form_tags as $form_tag) {
            if(!in_array($form_tag, $existing_tags)) {
                
                // Store tag i database if it doesnt exist
                $this->taggar->create([
                    'tag' => $form_tag
                ]);
            }
        }
//exit();
        
        //
        // Save tags relations in database
        //
        
        $this->qt = new \Idun\Questions_Tags\Questions_Tags();
        $this->qt->setDI($this->di);
        
        // Fetch all tags from database
        $all_tags = $this->taggar->findAll();
        //foreach ($all_tags as $tag) {
          //  $db_tags[] = $tag;
        //}
        
        // Fetch id for saved question
        $question = $this->questions->query("id")
                                    ->where("title = ?")
                                    ->execute(array($this->Value("title")));
        foreach ($question as $key) {
            $question_id[] = $key->id;
        }
        
        // Save keys in the Questions_Tags relations table
        foreach ($form_tags as $form_tag) {
            
            foreach ($all_tags as $key => $value) {
//echo "<pre>" . var_dump($value) . "</pre";
                if ($form_tag == $value->tag) {
                    
                    $this->qt->create([
                        'questions_id' => $question_id[0],
                        'tags_id' => $value->id
                    ]);
                }
            }
        }
        
//exit();

        return $save ? true : false;
    }


    /**
     * Callback What to do if the form was submitted?
     *
     */
    public function callbackSuccess()
    {
        
        $url = 'questions';
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