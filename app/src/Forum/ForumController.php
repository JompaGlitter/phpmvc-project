<?php

namespace Idun\Forum;

/**
 * A controller for users and admin related events.
 *
 */
class ForumController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;



    /**
     * Index page
     *
     * popular tags, most active users and most recent posts
     *
     * @return void
     *
     */
    public function viewIndexPageAction()
    {
        
        // Fetch resent questions
        $this->db->select('Q.id, Q.title, Q.username, Q.created, U.id AS user_id')
                 ->from('Questions AS Q')
                 ->join('Users AS U', 'U.username = Q.username')
                 ->orderBy('created DESC')
                 ->limit(3)
                 ->execute();
        $questions = $this->db->fetchAll();
        
        // Fetch all question related tags
        $this->db->select('T.*, Q.id AS question_id')
                 ->from('Tags AS T')
                 ->join('Questions_Tags AS QT', 'QT.tags_id = T.id')
                 ->join('Questions AS Q', 'Q.id = QT.questions_id')
                 ->execute();
        $tags = $this->db->fetchAll();
        
        // Fetch popular tags
        $this->db->select('T.*, COUNT(QT.tags_id) AS countOf')
                 ->from('Tags AS T')
                 ->join('Questions_Tags AS QT', 'QT.tags_id = T.id')
                 ->groupBy('tag')
                 ->orderBy('CountOf DESC')
                 ->limit(3)
                 ->execute();
        $p_tags = $this->db->fetchAll();
        
        // Fetch most active users
        $sql = '
            SELECT
	            U.id, U.username, U.gravatar, U.created,
	            (
	                (
	                    SELECT COUNT(Q.username)
	                    FROM Questions AS Q
	                    WHERE Q.username = U.username
	                ) +
	                (
	                    SELECT COUNT(A.username)
	                    FROM Answers AS A
	                    WHERE A.username = U.username
	                ) +
	                (
	                    SELECT COUNT(C.username)
	                    FROM Comments AS C
	                    WHERE C.username = U.username
	                )
	            ) AS total
            FROM
	            Users AS U
            ORDER BY
	            total DESC
            LIMIT 3;';
            
        $users = $this->db->executeFetchAll($sql);
        
        
        // Prepare som main page presentation.
        $this->theme->setVariable('main', "
                       <h2>Välkommen till Allt om Programmering</h2>
                       <p>Nedan listas de senaste frågorna, populäraste taggarna och mest aktiva användarna.</p>
                       ");
        
        // Dispatch most resent questions
        $this->dispatcher->forward([
            'controller' => 'forum',
            'action' => 'view-all-questions',
            'params' => [$questions, $tags]
        ]);
        
        // Dispatch popular tags
        $this->dispatcher->forward([
            'controller' => 'forum',
            'action' => 'view-all-tags',
            'params' => [$p_tags]
        ]);
        
        // Dispatch most active users
        $this->dispatcher->forward([
            'controller' => 'users',
            'action' => 'view-all-users',
            'params' => [$users]
        ]);
        
    }



    /**
     * View all questions
     *
     * @param 
     *
     * @return void
     *
     */
    public function viewAllQuestionsAction($i_questions = null, $i_tags = null) 
    {
        // Are values supplied from index page method?
        if (!is_null($i_questions) && !is_null($i_tags)) {
            $questions = $i_questions;
            $tags = $i_tags;
            
        } else {
            // Fetch all questions from database
            $this->db->select('Q.id, Q.title, Q.username, Q.created, U.id AS user_id')
                     ->from('Questions AS Q')
                     ->join('Users AS U','U.username = Q.username')
                     ->orderBy('created DESC')
                     ->execute();
            $questions = $this->db->fetchAll();
        
            // Fetch all question related tags from database
            $this->db->select('T.*, Q.id AS question_id')
                     ->from('Tags AS T')
                     ->join('Questions_Tags AS QT', 'QT.tags_id = T.id')
                     ->join('Questions AS Q', 'Q.id = QT.questions_id')
                     ->execute();
            $tags = $this->db->fetchAll();
        }
        
        $this->views->add('forum/all-questions', [
            'questions' => $questions,
            'tags' => $tags
        ]);
            
    }
    
    
    
    /**
     * View single question
     *
     * @param string $id, id of the selected question
     *
     * @return void
     *
     */
    public function viewQuestionAction($id) 
    {
        
        // Fetch question content
        $this->db->select('Q.*, U.id AS user_id')
                 ->from('Questions AS Q')
                 ->join('Users AS U', 'U.username = Q.username')
                 ->where('Q.id = ' . $id)
                 ->execute();
        $question = $this->db->fetchOne();
        
        // Fetch related tags
        $this->db->select('T.tag AS tag_name, T.id AS tag_id')
                 ->from('Tags AS T')
                 ->join('Questions_Tags AS QT', 'QT.tags_id = T.id')
                 ->join('Questions AS Q', 'Q.id = QT.questions_id')
                 ->where('Q.id = ' . $id)
                 ->execute();
        $tags = $this->db->fetchAll();
        
        // Fetch comments related to question
        $this->db->select('C.username, C.text, C.created, U.id AS user_id')
                 ->from('Comments AS C')
                 ->join('Questions AS Q', 'Q.id = C.question_id')
                 ->join('Users AS U', 'U.username = C.username')
                 ->where('Q.id = ' . $id)
                 ->orderBy('created ASC')
                 ->execute();
        $q_comments = $this->db->fetchAll();
        
        // Fetch answers related to question
        $this->db->select('A.id, A.username, A.text, A.created, U.id AS user_id')
                 ->from('Answers AS A')
                 ->join('Users AS U', 'U.username = A.username')
                 ->join('Questions AS Q', 'Q.id = A.question_id')
                 ->where('Q.id = ' . $id)
                 ->orderBy('created ASC')
                 ->execute();
        $answers = $this->db->fetchAll();
        
        // Fetch comments related to answers
        $this->db->select('C.answer_id, C.username, C.text, C.created, U.id AS user_id')
                 ->from('Comments AS C')
                 ->join('Answers AS A', 'A.id = C.answer_id')
                 ->join('Questions AS Q', 'Q.id = A.question_id')
                 ->join('Users AS U', 'U.username = C.username')
                 ->where('Q.id = ' . $id)
                 ->orderBy('created ASC')
                 ->execute();
        $a_comments = $this->db->fetchAll();
        
        // Create view and supply content
        $this->theme->setTitle($question->title);
        $this->views->add('forum/single-question', [
            'id' => $id,
            'question' => $question,
            'tags' => $tags,
            'q_comments' => $q_comments,
            'answers' => $answers,
            'a_comments' => $a_comments,
        ]);
        
    }
    
    
    
    /**
     * View all tags
     *
     * @return void
     *
     */
    public function viewAllTagsAction($p_tags = null) 
    {
        if (!is_null($p_tags)){
            $tags = $p_tags;
        
        } else {
        
        $this->db->select('T.*, count(QT.tags_id) AS countOf')
                 ->from('Tags AS T')
                 ->join('Questions_Tags AS QT', 'QT.tags_id = T.id')
                 ->groupBy('T.tag')
                 ->orderBy('T.tag ASC')
                 ->execute();
        $tags = $this->db->fetchAll();
        }
        
        $this->views->add('forum/all-tags', [
            'tags' => $tags,
        ]);
        
    }
    
    
    
    /**
     * View single tag with a list of related questions
     *
     * @param string $id, id of the selected tag
     *
     * @return void
     *
     */
    public function viewTagAction($id)
    {
        
        // Fetch tag based on id
        $this->db->select('tag')
                 ->from('Tags')
                 ->where('id = ' . $id)
                 ->execute();
        $tag = $this->db->fetchOne();
        
        // Fetch questions based on tag id
        $this->db->select('Q.id AS q_id, Q.title AS q_title, Q.username AS q_username, Q.created AS q_created')
                 ->from('Questions AS Q')
                 ->join('Questions_Tags AS QT', 'QT.questions_id = Q.id')
                 ->join('Tags AS T', 'T.id = QT.tags_id')
                 ->where('T.id = ' . $id)
                 ->orderBy('created')
                 ->execute();
        $questions = $this->db->fetchAll();
        
        // Fetch all question related tags
        $this->db->select('T.*, Q.id AS question_id')
                 ->from('Tags AS T')
                 ->join('Questions_Tags AS QT', 'QT.tags_id = T.id')
                 ->join('Questions AS Q', 'Q.id = QT.questions_id')
                 ->execute();
        $q_tags = $this->db->fetchAll();
        
        // Create view and supply content
        $this->theme->setTitle($tag->tag . ' | taggar');
        $this->views->add('forum/single-tag', [
            'tag' => $tag,
            'questions' => $questions,
            'q_tags' => $q_tags,
        ]);
        
    }
    
    
    
    /**
     * Add new question
     *
     * @return void
     */
    public function addQuestionAction()
    {
        // Is user logged in?
        if ($this->session->has('username')) {
        
            // Get username from session
            $username = $this->session->get('username');
            
            // Fetch existing tags from database
            $this->db->select('T.*, count(QT.tags_id) AS countOf')
                     ->from('Tags AS T')
                     ->join('Questions_Tags AS QT', 'QT.tags_id = T.id')
                     ->groupBy('T.tag')
                     ->orderBy('T.tag ASC')
                     ->execute();
            $dbTags = $this->db->fetchAll();
            
            // Create question form
            $form = new \Idun\HTMLForm\FormAddQuestion($username, $dbTags);
            $form->setDI($this->di);
            $form->check();

            $this->theme->setTitle("Skapa ny fråga");
            $this->views->add('default/page', [
                'title' => "Skapa ny fråga",
                'content' => $form->getHTML()
            ]);
            
                $this->dispatcher->forward([
                    'controller' => 'forum',
                    'action' => 'view-all-tags'
                ]);
        
        } else {
            
            // Redirect to login page
            $url = $this->url->create('login');
            header("Location: $url");
        }
        
    }
    
    
    
    /**
     * Add answer to a question
     *
     * @param int $id, id of question
     *
     * @return void
     */
    public function addAnswerAction($id)
    {
        if (!isset($id)) {
            die("Missing id");
        }
        
        // Is user logged in?
        if ($this->session->has('username')) {
        
            // Get username from session
            $username = $this->session->get('username');
            
            // Create answer form
            $form = new \Idun\HTMLForm\FormAddAnswer($username, $id);
            $form->setDI($this->di);
            $form->check();
        
            $this->theme->setTitle('Svara på fråga');
            $this->views->add('default/page', [
                'title' => 'Svara på fråga',
                'content' => $form->getHTML()
            ]);
        
        
            /*
             * Display question
             */
        
            // Fetch question content
            $this->db->select('Q.*, U.id AS user_id')
                     ->from('Questions AS Q')
                     ->join('Users AS U', 'U.username = Q.username')
                     ->where('Q.id = ' . $id)
                     ->execute();
            $question = $this->db->fetchOne();
        
            // Fetch related tags
            $this->db->select('T.tag AS tag_name, T.id AS tag_id')
                     ->from('Tags AS T')
                     ->join('Questions_Tags AS QT', 'QT.tags_id = T.id')
                     ->join('Questions AS Q', 'Q.id = QT.questions_id')
                     ->where('Q.id = ' . $id)
                     ->execute();
            $tags = $this->db->fetchAll();
        
            $this->views->add('forum/answer-question', [
                'question' => $question,
                'tags' => $tags
            ]);
        
        } else {
            
            // Redirect to login page
            $url = $this->url->create('login');
            header("Location: $url");
        }
        
    }
    
    
    
    /**
     * Add comment on a question
     *
     * @param int $id, id of the question
     *
     * @return void
    */
    public function addQuestionCommentAction($id)
    {
        
        if (!isset($id)) {
            die("Missing id");
        }
        
        // Is user logged in?
        if ($this->session->has('username')) {
        
            // Get username from session
            $username = $this->session->get('username');
            
            // Create question comment form
            $form = new \Idun\HTMLForm\FormAddQuestionComment($username, $id);
            $form->setDI($this->di);
            $form->check();
        
            $this->theme->setTitle('Kommentera på frågan');
            $this->views->add('default/page', [
                'title' => 'Kommentera på frågan',
                'content' => $form->getHTML()
            ]);
            
        
            /*
             * Display question
             */
        
            // Fetch question content
            $this->db->select('Q.*, U.id AS user_id')
                     ->from('Questions AS Q')
                     ->join('Users AS U', 'U.username = Q.username')
                     ->where('Q.id = ' . $id)
                     ->execute();
            $question = $this->db->fetchOne();
        
            // Fetch related tags
            $this->db->select('T.tag AS tag_name, T.id AS tag_id')
                     ->from('Tags AS T')
                     ->join('Questions_Tags AS QT', 'QT.tags_id = T.id')
                     ->join('Questions AS Q', 'Q.id = QT.questions_id')
                     ->where('Q.id = ' . $id)
                     ->execute();
            $tags = $this->db->fetchAll();
        
            $this->views->add('forum/answer-question', [
                'question' => $question,
                'tags' => $tags
            ]);
        
        } else {
            
            // Redirect to login page
            $url = $this->url->create('login');
            header("Location: $url");
        }
        
    }
    
    
    
    /**
     * Add comment on an answer
     *
     * @param int $id, id of the answer to comment on
     *
     * @return void
     */
    public function addAnswerCommentAction($id)
    {
        
        if (!isset($id)) {
            die("Missing id");
        }
        
        // Is user logged in?
        if ($this->session->has('username')) {
        
            // Get username from session
            $username = $this->session->get('username');
            
            // Fetcher id of question for redirection purpose
            $this->db->select('Q.id')
                     ->from('Questions AS Q')
                     ->join('Answers AS A', 'A.question_id = Q.id')
                     ->where('A.id = ' . $id)
                     ->execute();
            $question = $this->db->fetchOne();
        
            // Create answer comment form
            $form = new \Idun\HTMLForm\FormAddAnswerComment($username, $id, $question->id);
            $form->setDI($this->di);
            $form->check();
        
            $this->theme->setTitle('Kommentera på svaret');
            $this->views->add('default/page', [
                'title' => 'Kommentera på svaret',
                'content' => $form->getHTML()
            ]);
        
        } else {
            
            // Redirect to login page
            $url = $this->url->create('login');
            header("Location: $url");
        }
        
    }
    

}