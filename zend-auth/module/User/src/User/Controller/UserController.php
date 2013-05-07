<?php

namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Session\Container;

use User\Form\LoginForm;
use User\Model\User;
use User\Form\AccountForm;

class UserController extends AbstractActionController
{
    protected $userTable;
    
    public function getUserTable(){
    	if (! $this->userTable) {
    		$sm = $this->getServiceLocator ();
    		$this->userTable = $sm->get ( 'User\Model\UserTable' );
    	}
    	return $this->userTable;
    }

    public function indexAction(){
        $auth = new AuthenticationService();

        $identity = null;
        $logged = null;
        if ($auth->hasIdentity()) {
            $identity = $auth->getIdentity();
            $session = new Container('user');
            $session->offsetSet('username',$identity);
            $logged = $session->offsetGet('username');
        }
        
        if ($logged === null):
            $this->redirect()->toRoute('user', array('action' => 'signin'));
        endif;
        
        return array(
            'logged' => $logged,
        );
    }
    
    public function signupAction(){
        $form = new AccountForm();
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $user = new User();
            $form->setData($request->getPost());
            if ($form->isValid()) {
            	$user->exchangeArray ($form->getData());
            	$this->getUserTable()->createAccount($user);
            	return $this->redirect()->toRoute('user');
            }
        }
        
        return array('form' => $form);
    }
    
    public function signinAction(){   
        $form = new LoginForm();

        $request = $this->getRequest();
        if ($request->isPost()){
            $post = $request->getPost();

            $sm = $this->getServiceLocator();
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

            $authAdapter = new AuthAdapter($dbAdapter);

            $authAdapter->setTableName('user')
                    ->setIdentityColumn('username')
                    ->setCredentialColumn('password');

            $authAdapter->setIdentity($post->get('username'))
                    ->setCredential(sha1($post->get('password')));

            $authService = new AuthenticationService();
            $authService->setAdapter($authAdapter);

            $result = $authService->authenticate();

            if ($result->isValid()){
                return $this->redirect()->toRoute('user');
            }
            else{
                echo '<h3>Login failed !</h3>';
            }
        }
        return array('form' => $form);
    }
    
    public function signoutAction(){
        $auth = new AuthenticationService();
        $auth->clearIdentity();
        $session = new Container('user');
        $session->offsetUnset('username');
        return $this->redirect()->toRoute('user');
    }
}