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
    
    
    
    /**
     * Setup database and populate with default content
     *
     */
    public function setupAction()
    {
        
        $sql = "/* 
 * create User table with content
 */
DROP TABLE IF EXISTS Users;
CREATE TABLE Users 
(
	id INT NOT NULL AUTO_INCREMENT,
	username VARCHAR(100) NOT NULL,
	email VARCHAR(100),
	password VARCHAR(255) NOT NULL,
	about TEXT,
	homepage VARCHAR(255),
	gravatar VARCHAR(255) NOT NULL,
	created DATETIME,
	updated DATETIME,
	PRIMARY KEY (id)
) ENGINE = InnoDB;

INSERT INTO Users (username, password, about, email, homepage, gravatar, created) VALUES
(
	'JompaGlitter',
	'$2y$10\$FYUPUNX9Dt.JU0cS9hgkKuMn8S83cvXSKnijy0RCGzysz09j5Gd2u',
	'En blivande programmerare',
	'jonfredelius@gmail.com',
	'http://www.jonfredelius.com',
	'http://www.gravatar.com/avatar/default_image',
	NOW()
),
(
	'Venlisch',
	'$2y$10$7BGfnsP1WzvlPExga1rnBeqwW8hWjA4aSrri1BayXX0ksar1e/d8a',
	'Musiklärare som även programmerar lite',
	'venla@hinnemo.se',
	NULL,
	'http://www.gravatar.com/avatar/default_image',
	NOW()
),
(
	'Joshua',
	'$2y$10\$rngy4YnM5HeStWzoACTlCOTWMfQTlyIdGwrtoIaC4us0/686I.f2e',
	'Elit-surfare med smak för kodning',
	'joshua.ladimer@live.com',
	NULL,
	'http://www.gravatar.com/avatar/default_image',
	NOW()
),
(
	'fliXen',
	'$2y$10\$bcwj9SR7731LbOuMTrzI1OQD10z9P4RQrO.a3ibwq/KnD5zEbRM2K',
	'En dryg sate med inget intresse för programmering. Att jävlas är däremot kul!',
	'flixen@hotmail.com',
	NULL,
	'http://www.gravatar.com/avatar/default_image',
	NOW()
);


/*
 * create Question table with content
 */
DROP TABLE IF EXISTS Questions;
CREATE TABLE Questions 
(
	id INT NOT NULL AUTO_INCREMENT,
	username VARCHAR(100) NOT NULL,
	title VARCHAR(255) NOT NULL,
	text TEXT NOT NULL,
	created DATETIME,
	updated DATETIME,
	PRIMARY KEY (id)
) ENGINE = InnoDB;

INSERT INTO Questions (username, title, text, created) VALUES
(
	'JompaGlitter',
	'Vilket programmeringsspråk?',
	'Vilket programmeringsspråk ska man satsa på framöver? PHP, JavaScript? Eller något annat?',
	NOW()
),
(
	'Venlisch',
	'Att programmera eller inte',
	'Ska man programmera eller skriva musik, det är frågan. Vad tycker ni?',
	NOW()
);


/*
 * create Answer table with content
 */
DROP TABLE IF EXISTS Answers;
CREATE TABLE Answers 
(
	id INT NOT NULL AUTO_INCREMENT,
	username VARCHAR(100) NOT NULL,
	question_id INT NOT NULL,
	text TEXT NOT NULL,
	created DATETIME,
	PRIMARY KEY (id)
) ENGINE = InnoDB;

INSERT INTO Answers (username, question_id, text, created) VALUES
(
	'Idun',
	1,
	'Du ska lätt satsa på JavaScript, det är störst efterfrågan på den kompetensen just nu.',
	NOW()
),
(
	'fliXen',
	2,
	'Äh, skit i allt och spela tv-spel och kolla film istället! =D',
	NOW()
);


/*
 * create Comments table with content
 */
DROP TABLE IF EXISTS Comments;
CREATE TABLE Comments 
(
	id INT NOT NULL AUTO_INCREMENT,
	username VARCHAR(100) NOT NULL,
	question_id INT,
	answer_id INT,
	text TEXT NOT NULL,
	created DATETIME,
	PRIMARY KEY (id)
) ENGINE = InnoDB;

INSERT INTO Comments (username, question_id, answer_id, text, created) VALUES
(
	'Venlisch',
	1,
	NULL,
	'Den här frågan har varit uppe förut och fått ett bra svar.',
	NOW()	
),
(
	'Joshua',
	NULL,
	1,
	'Jag håller med dig till viss del, men PHP är alltid bra att ha också, speciellt tillsammans med SQL.',
	NOW()
),
(
	'JompaGlitter',
	NULL,
	2,
	'Så tråkigt är det inte med programmering! Nu tycker jag att du är lite väl negativ...',
	NOW()
),
(
	'fliXen',
	NULL,
	2,
	'Men du då!',
	NOW()
);


/*
 * create Tags table with content
 */
DROP TABLE IF EXISTS Tags;
CREATE TABLE Tags
(
	id INT NOT NULL AUTO_INCREMENT,
	tag VARCHAR(100),
	PRIMARY KEY (id)
) ENGINE = InnoDB;

INSERT INTO Tags (tag) VALUES ('sql'), ('php'), ('javascript');


/*
 * create ER table between Questions and Tags
 */
DROP TABLE IF EXISTS Questions_Tags;
CREATE TABLE Questions_Tags
(
	questions_id INT NOT NULL,
	tags_id INT NOT NULL
) ENGINE = InnoDB;

INSERT INTO Questions_Tags (questions_id, tags_id) Values (1, 1), (1, 2), (1, 3), (2, 1), (2, 2);
";

        $this->db->execute($sql);
        
        $url = $this->url->create('');
        header("Location: $url");
        
    }
    

}