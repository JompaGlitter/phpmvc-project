<?php

namespace Idun\Users;

/**
 * A controller for users and admin related events.
 *
 */
class UsersController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;
    
    
   
    /**
     * View all users.
     *
     * @return void
     */
    public function viewAllUsersAction($i_users = null)
    {
        if (!is_null($i_users)) {
            $users = $i_users;
            
        } else {
        $this->db->select('id, username, gravatar, created')
                 ->from('Users')
                 ->orderBy('username ASC')
                 ->execute();
        
        $users = $this->db->fetchAll();
        }
        
        $this->views->add('users/list-all', [
            'users' => $users,
            'title' => "Användare"
        ]);
    }
    
    
    
    /**
     * View single user based on id.
     *
     * @param int $id of user to display
     *
     * @return void
     */
    public function idAction($id = null)
    {
        $this->db->select()
                 ->from('Users AS U')
                 ->where('U.id = ' . $id)
                 ->execute();
        $user = $this->db->fetchOne();
 
        $this->theme->setTitle("Detaljer för användare");
        
        $this->views->add('users/view', [
            'user'  => $user
        ]);
        
        
        $this->dispatcher->forward([
            'controller' => 'users',
            'action'     => 'user-posts',
            'params'     => [$id]
        ]);
        
    }
    
    
    
    /**
     * Add new user.
     *
     * @return void
     */
    public function addAction()
    {

        $form = new \Idun\HTMLForm\FormAddUser();
        $form->setDI($this->di);
        $form->check();

        $this->theme->setTitle("Lägg till ny användare");
        $this->views->add('default/page', [
            'title' => "Lägg till ny användare",
            'content' => $form->getHTML()
        ]);

    }



    /**
     * Update user.
     *
     * @param string $id of user to update.
     *
     * @return void
     */
    public function updateAction($id = null)
    {
        if (!isset($id)) {
            die("Missing id");
        }

        $this->db->select('username, about, email, homepage AS url')
                 ->from('Users')
                 ->where('id = ' . $id)
                 ->execute();
        $user = $this->db->fetchOne();

        $username = $user->username;
        $about = $user->about;
        $email = $user->email;
        $url = $user->url;

        $form = new \Idun\HTMLForm\FormUpdateUser($id, $username, $about, $email, $url);
        $form->setDI($this->di);
        $status = $form->check();

        $this->theme->setTitle("Redigera användare");
        $this->views->add('default/page', [
            'title' => "Redigera användare",
            'content' => $form->getHTML()
        ]);
    } 
    
    
    
    /**
     * Display all posts (questions and answers) related to user
     *
     * @param $id, users id
     *
     * @return void
     */
    public function userPostsAction($id)
    {
        
        // Fetch all questions related to user
        $this->db->select('Q.id, Q.title, Q.created')
                 ->from('Questions AS Q')
                 ->join('Users AS U', 'U.username = Q.username')
                 ->where('U.id = ' . $id)
                 ->orderBy('created ASC')
                 ->execute();
        $questions = $this->db->fetchAll();
        
        // Fetch all question related tags
        $this->db->select('T.*, Q.id AS question_id')
                 ->from('Tags AS T')
                 ->join('Questions_Tags AS QT', 'QT.tags_id = T.id')
                 ->join('Questions AS Q', 'Q.id = QT.questions_id')
                 ->execute();
        $tags = $this->db->fetchAll();
        
        // Fetch all questions the user has posted an answer to
        $this->db->select('Q.id AS question_id, Q.title AS question_title, A.created AS answer_created')
                 ->from('Questions AS Q')
                 ->join('Answers AS A', 'A.question_id = Q.id')
                 ->join('Users AS U', 'U.username = A.username')
                 ->where('U.id = ' . $id)
                 ->execute();
        $answers = $this->db->fetchAll();
        
        // Create view and supply content
        $this->views->add('users/user-posts', [
            'questions' => $questions,
            'tags'      => $tags,
            'answers'   => $answers
        ]);
        
    }
    
    

} 