<?php
namespace App\Controller;

use Cake\Event\Event;
use App\Controller\AppController;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
    public $components = array('Auth');

    public function beforeFilter(Event $event = null) {
        $this->Auth->allow(['add']);
    }

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        // if the admin session hasn't been set
        if (!($this->Auth->user()['admin'] > 1)) {
            // set flash message and redirect
            $this->Flash->set('You must be an admin to access this page.', ['element' => 'error']);
            return $this->redirect('/');
        }
        $this->set('users', $this->paginate($this->Users));
        $this->set('_serialize', ['users']);
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        // if the admin session hasn't been set
        if (!($this->Auth->user()['admin'] > 1)) {
            // set flash message and redirect
            $this->Flash->set('You must be an admin to access this page.', ['element' => 'error']);
            return $this->redirect('/');
        }
        $user = $this->Users->get($id, [
            'contain' => []
        ]);
        $this->set('user', $user);
        $this->set('_serialize', ['user']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $this->request->data['lat'] = null;
        $this->request->data['lng'] = null;
        $this->request->data['dlat'] = null;
        $this->request->data['dlng'] = null;
        $this->request->data['admin'] = 0;
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                //var_dump($user);die();
                $this->Flash->success(__('The user has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                //var_dump($user);die();
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

    public function adminAdd()
    {
        if (!($this->Auth->user()['admin'] > 1)) {
            // set flash message and redirect
            $this->Flash->set('You must be an admin to access this page.', ['element' => 'error']);
            return $this->redirect('/');
        }
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        // if the admin session hasn't been set
        if (!($this->Auth->user()['admin'] > 1)) {
            // set flash message and redirect
            $this->Flash->set('You must be an admin to access this page.', ['element' => 'error']);
            return $this->redirect('/');
        }
        $user = $this->Users->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        // if the admin session hasn't been set
        if (!($this->Auth->user()['admin'] > 1)) {
            // set flash message and redirect
            $this->Flash->set('You must be an admin to access this page.', ['element' => 'error']);
            return $this->redirect('/');
        }
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function login()
    {
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Auth->setUser($user);
                return $this->redirect($this->Auth->redirectUrl());
            }
            $this->Flash->error('Your username or password is incorrect.');
        }
    }

    public function logout()
    {
        $this->Flash->success('You are now logged out.');
        return $this->redirect($this->Auth->logout());
    }
}
