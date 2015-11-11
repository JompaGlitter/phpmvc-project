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
        $this->db->select('Q.id, Q.title, Q.username, Q.created')
                 ->from('Questions AS Q')
                 ->orderBy('created ASC')
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
        $this->db->select('T.*, COUNT(QT.tags_id) AS CountOf')
                 ->from('Tags AS T')
                 ->join('Questions_Tags AS QT', 'QT.tags_id = T.id')
                 ->groupBy('tag')
                 ->orderBy('CountOf DESC')
                 ->limit(3)
                 ->execute();
        $p_tags = $this->db->fetchAll();
        
        
        // Count questions
        $sql_question = $this->db->select('COUNT(Q.username)')
                                 ->from('Questions AS Q')
                                 ->where('Q.username = U.username');
        // Count answers
        $sql_answer = $this->db->select('COUNT(A.username)')
                               ->from('Answers AS A')
                               ->where('A.username = U.username');
        // Count comments
        $sql_comments = $this->db->select('COUNT(C.username)')
                                 ->from('Comments AS C')
                                 ->where('C.username = U.username');
        
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
        $this->db->select('Q.id, Q.title, Q.username, Q.created')
                 ->from('Questions AS Q')
                 ->orderBy('created ASC')
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
        $this->db->select()
                 ->from('Questions')
                 ->where('id = ' . $id)
                 ->execute();
        $question = $this->db->fetchOne();
        
        // Fetch related tags
        $this->db->select('T.tag AS tag_name')
                 ->from('Tags AS T')
                 ->join('Questions_Tags AS QT', 'QT.tags_id = T.id')
                 ->join('Questions AS Q', 'Q.id = QT.questions_id')
                 ->where('Q.id = ' . $id)
                 ->execute();
        $tags = $this->db->fetchAll();
        
        // Fetch comments related to question
        $this->db->select('C.username, C.text, C.created')
                 ->from('Comments AS C')
                 ->join('Questions AS Q', 'Q.id = C.question_id')
                 ->where('Q.id = ' . $id)
                 ->orderBy('created ASC')
                 ->execute();
        $q_comments = $this->db->fetchAll();
        
        // Fetch answers related to question
        $this->db->select('A.id, A.username, A.text, A.created')
                 ->from('Answers AS A')
                 ->join('Questions AS Q', 'Q.id = A.question_id')
                 ->where('Q.id = ' . $id)
                 ->orderBy('created ASC')
                 ->execute();
        $answers = $this->db->fetchAll();
        
        // Fetch comments related to answers
        $this->db->select('C.answer_id, C.username, C.text, C.created')
                 ->from('Comments AS C')
                 ->join('Answers AS A', 'A.id = C.answer_id')
                 ->join('Questions AS Q', 'Q.id = A.question_id')
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
        
        $this->db->select()
                 ->from('Tags')
                 ->orderBy('tag ASC')
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
    
    

}