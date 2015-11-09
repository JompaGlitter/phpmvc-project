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
    public function viewAllUsersAction()
    {
        
        $this->db->select('id, username, gravatar, created')
                 ->from('Users')
                 ->orderBy('username ASC')
                 ->execute();
        
        $users = $this->db->fetchAll();
 
        $this->views->add('users/list-all', [
            'users' => $users,
            'title' => "Användare"
        ]);
    }
    
    
    /**
     * List all active and not deleted users.
     *
     * @return void
     */
    public function activeAction()
    {
        $all = $this->users->query()
            ->where('active IS NOT NULL')
            ->andWhere('deleted IS NULL')
            ->execute();
 
        $this->theme->setTitle("Aktiva användare");
        $this->views->add('users/list-all', [
            'users' => $all,
            'title' => "Aktiva användare",
        ]);
    }
    
    
    /**
     * List all inactive and not deleted users.
     *
     * @return void
     */
    public function isInactiveAction()
    {
        $all = $this->users->query()
            ->where('active IS NULL')
            ->andWhere('deleted IS NULL')
            ->execute();
 
        $this->theme->setTitle("Inaktiva användare");
        $this->views->add('users/list-all', [
            'users' => $all,
            'title' => "<i class='fa fa-ban'></i> Inaktiva användare",
        ]);
    }
    
    
    /**
     * List all soft-deleted users.
     *
     * @return void
     */
    public function softDeletedAction()
    {
        $all = $this->users->query()
            ->Where('deleted is NOT NULL')
            ->execute();
 
        $this->theme->setTitle("Papperskorgen");
        $this->views->add('users/list-all-deleted', [
            'users' => $all,
            'title' => "<i class='fa fa-trash-o'></i> Papperskorgen",
        ]);
    }
    
    
    /**
     * List user with id.
     *
     * @param int $id of user to display
     *
     * @return void
     */
    public function idAction($id = null)
    {
        $user = $this->users->find($id);
 
        $this->theme->setTitle("Detaljer för användare");
        $this->views->add('users/view', [
            'user'  => $user,
            'title' => "Detaljer för användare: ",
        ]);
    }
    
    
    /**
     * Add new user.
     *
     * @param string $acronym of user to add.
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

        $user = $this->users->find($id);

        $acronym = $user->acronym;
        $name = $user->name;
        $email = $user->email;

        $form = new \Idun\HTMLForm\FormUpdateUser($id, $acronym, $name, $email);
        $form->setDI($this->di);
        $status = $form->check();

        $this->di->theme->setTitle("Redigera användare");
        $this->di->views->add('default/page', [
            'title' => "Redigera användare",
            'content' => $form->getHTML()
        ]);
    } 
    
    
    /**
     * Delete user.
     *
     * @param integer $id of user to delete.
     *
     * @return void
     */
    public function deleteAction($id = null)
    {
        if (!isset($id)) {
            die("Missing id");
        }
 
        $res = $this->users->delete($id);
 
        $url = $this->url->create('users');
        $this->response->redirect($url);
    }
    
    
    /**
     * Delete (soft) user.
     *
     * @param integer $id of user to delete.
     *
     * @return void
     */
    public function softDeleteAction($id = null)
    {
        if (!isset($id)) {
            die("Missing id");
        }
 
        $now = gmdate('Y-m-d H:i:s');
 
        $user = $this->users->find($id);
 
        $user->deleted = $now;
        $user->save();
 
        $url = $this->url->create('users/id/' . $id);
        $this->response->redirect($url);
    }
    
    
    /**
     * Undo delete (soft) user.
     *
     * @param integer $id of user to undo delete on.
     *
     * @return void
     */
    public function undoSoftDeleteAction($id = null)
    {
        if (!isset($id)) {
            die("Missing id");
        }
 
        $user = $this->users->find($id);
        
        if (is_null($user->deleted)) {
            die("Can not undo delete, user is already active.");
        } else {
            $user->deleted = null;
        }
        $user->save();
 
        $url = $this->url->create('users/id/' . $id);
        $this->response->redirect($url);
    }
    
    
    /**
     * Activate inactive user.
     *
     * @param integer $id of user to activate.
     *
     * @return void
     */
    public function activateAction($id = null)
    {
        if (!isset($id)) {
            die("Missing id");
        }

        date_default_timezone_set('Europe/Berlin');
        $now = date('Y-m-d H:i:s');

        $user = $this->users->find($id);

        $user->active = $now;
        $user->save();

        $url = $this->url->create('users/id/' . $id);
        $this->response->redirect($url);
    }

    /**
     * Deactivate active user.
     *
     * @param integer $id of user to deactivate.
     *
     * @return void
     */
    public function deactivateAction($id = null)
    {
        if (!isset($id)) {
            die("Missing id");
        }

        $user = $this->users->find($id);

        $user->active = NULL;
        $user->save();

        $url = $this->url->create('users/id/' . $id);
        $this->response->redirect($url);
    } 
    
    
    /**
     * Restore/setup user database and setup two example users.
     *
     *
     * @return void
     */
    public function setupUsersAction()
    {
        
        $this->db->dropTableIfExists('user')->execute();

        $this->db->createTable(
            'user',
            [
                'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
                'acronym' => ['varchar(20)', 'unique', 'not null'],
                'email' => ['varchar(80)'],
                'name' => ['varchar(80)'],
                'password' => ['varchar(255)'],
                'created' => ['datetime'],
                'updated' => ['datetime'],
                'deleted' => ['datetime'],
                'active' => ['datetime'],
            ]
        )->execute();

        $this->db->insert(
            'user',
            ['acronym', 'email', 'name', 'password', 'created', 'active']
        );

        date_default_timezone_set('Europe/Berlin');
        $now = date('Y-m-d H:i:s');

        $this->db->execute([
            'admin',
            'admin@dbwebb.se',
            'Administrator',
            password_hash('admin', PASSWORD_DEFAULT),
            $now,
            $now
        ]);

        $this->db->execute([
            'doe',
            'doe@dbwebb.se',
            'John/Jane Doe',
            password_hash('doe', PASSWORD_DEFAULT),
            $now,
            $now
        ]);

        $url = $this->url->create('users');
        $this->response->redirect($url);
    }

} 