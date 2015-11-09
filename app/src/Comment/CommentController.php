<?php

namespace Idun\Comment;

/**
 * To attach comments-flow to a page or some content.
 *
 */
class CommentController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;



    /**
     * Initialize the controller.
     *
     * @return void
     */
    public function initialize()
    {
        $this->comments = new \Idun\Comment\Comment();
        $this->comments->setDI($this->di);
    }
    
    
    /**
     * View all comments.
     *
     * @return void
     */
    public function viewAction()
    {
        $this->db->select()
                 ->from('Comments')
                 ->execute();
        
        $all = $this->db->fetchAll();

        $this->views->add('default/test', [
            'content' => $all,
        ]);
    }



    /**
     * Add a comment.
     *
     * @return void
     */
    public function addAction($pagekey = null)
    {
        $ip = $this->request->getServer('REMOTE_ADDR');

        $form = new \Idun\HTMLForm\FormAddComment($pagekey, $ip);
        $form->setDI($this->di);
        $form->check();
        
        $this->views->add('default/page', [
            'title' => "Lägg till kommentar",
            'content' => $form->getHTML()
        ]);
    }



    /**
     * Show form to edit comment
     *
     *
     *
     */
    public function editViewAction($id = null) 
    {
        if (!isset($id)) {
            die("Missing id");
        }
        
       $comment = $this->comments->find($id);
       
       $content = $this->textFilter->doFilter($comment->content, "markdown, nl2br");
       $name = $comment->name;
       $web = $comment->web;
       $email = $comment->email;
       $pagekey = $comment->pagekey;
       
       $form = new \Idun\HTMLForm\FormEditComment($id, $content, $name, $web, $email, $pagekey);
       $form->setDI($this->di);
       $status = $form->check();
       
       $this->theme->setTitle("Redigera kommentar");
       $this->views->add('default/page', [
            'title' => "Redigera kommentar",
            'content' => $form->getHTML()
        ]);
    }



    /**
     * Remove a comment
     *
     *
     *
     */
    public function deleteAction($id, $pagekey)
    {
        $isPosted = $this->request->getPost('doRemoveOne');
        
        if (!$isPosted) {
            $this->response->redirect($this->request->getPost('redirect'));
        }
        
        $this->comments->deleteOne($id, $pagekey);
        
        $this->response->redirect($this->request->getPost('redirect'));
    }



    /**
     * Remove all comments.
     *
     * @return void
     */
    public function removeAllAction($pagekey)
    {
        $comments = $this->comments->query()
            ->where('pagekey = ?')
            ->execute(array($pagekey));
        
        foreach ($comments as $comment) {
            $this->comments->delete($comment->getProperties()['id']);
        }
        

        $url = $pagekey == 'smalltalk' ? $this->url->create('smalltalk') : $this->url->create('');
        $this->response->redirect($url);
    }
    
    
    /**
     * Restore/setup comment database and setup two example comments.
     *
     *
     * @return void
     */
    public function setupCommentsAction($pagekey = null)
    {
        $this->db->dropTableIfExists('comment')->execute();

        $this->db->createTable(
            'comment',
            [
                'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
                'content' => ['text'],
                'name' => ['varchar(80)'],
                'web' => ['varchar(80)'],
                'email' => ['varchar(80)'],
                'timestamp' => ['datetime'],
                'pagekey' => ['varchar(20)']
            ]
        )->execute();

        $this->db->insert(
            'comment',
            ['content', 'name', 'web', 'email', 'timestamp', 'ip', 'pagekey']
        );
        
        date_default_timezone_set('Europe/Berlin');
        $now = date('Y-m-d H:i:s');

        $this->db->execute([
            'Välkommen! Detta är en test-kommentar.',
            'Admin',
            'http://www.99mac.se',
            'admin@mail.se',
            $now,
            $this->request->getServer('REMOTE_ADDR'),
            'smalltalk'
        ]);

        $this->db->execute([
            'Och så en testkommentar till...',
            'Admins bror',
            'http://www.dn.se',
            'stalker@aroundtheclock.com',
            $now,
            $this->request->getServer('REMOTE_ADDR'),
            'me'
        ]);

        $url = $pagekey == 'smalltalk' ? $this->url->create('smalltalk') : $this->url->create('');
        $this->response->redirect($url);
    }
    
    
}
