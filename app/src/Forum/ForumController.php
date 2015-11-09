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
     * View all questions
     *
     * @return void
     */
    public function viewAllQuestionsAction() 
    {
        // Fetch all questions from database
        $this->db->select('Q.id, Q.title, Q.username, Q.created')
                 ->from('Questions AS Q')
                 ->orderBy('created ASC')
                 ->execute();
        
        $questions = $this->db->fetchAll();
        
        // Fetch all question related tags from database
        $this->db->select('T.tag AS tag_name, Q.id AS question_id')
                 ->from('Tags AS T')
                 ->join('Questions_Tags AS QT', 'QT.tags_id = T.id')
                 ->join('Questions AS Q', 'Q.id = QT.questions_id')
                 ->execute();
        
        $tags = $this->db->fetchAll();
        
        $this->views->add('forum/all-questions', [
            'questions' => $questions,
            'tags' => $tags
        ]);
        
    }
    
    
    /**
     * View single question
     *
     * @return void
     */
    public function viewQuestionAction($id = null) 
    {
        // Fetch question content
        $this->db->select()
                 ->from('Questions')
                 ->where('id = ' . $id)
                 ->execute();
        $question = $this->db->fetchAll();
        
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
        $this->db->select('A.username, A.text, A.created')
                 ->from('Answers AS A')
                 ->join('Questions AS Q', 'Q.id = A.question_id')
                 ->where('Q.id = ' . $id)
                 ->orderBy('created ASC')
                 ->execute();
        $answers = $this->db->fetchAll();
        
        // Fetch comments related to answers
        $this->db->select('C.username, C.text, C.created')
                 ->from('Comments AS C')
                 ->join('Answers AS A', 'A.id = C.answer_id')
                 ->join('Questions AS Q', 'Q.id = A.question_id')
                 ->where('Q_id = ' . $id)
                 ->orderBy('created ASC')
                 ->execute();
        $a_comments = $this->db->fetchAll();
    }
    
    
    /**
     * View all tags
     *
     * @return void
     */
    public function viewAllTagsAction() 
    {
        
        $this->db->select('tag')
                 ->from('Tags')
                 ->orderBy('tag ASC')
                 ->execute();
        
        $all = $this->db->fetchAll();
        
        $this->views->add('forum/all-tags', [
            'content' => $all,
        ]);
        
    }

}