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
    	if (!$this->userTable){
    		$sm = $this->getServiceLocator();
    		$this->userTable = $sm->get('User\Model\UserTable');
    	}
    	return $this->userTable;
    }

    public function indexAction(){
        $auth = new AuthenticationService();

        $identity = null;
        $logged = null;
        if ($auth->hasIdentity()){
            $identity = $auth->getIdentity();
            $session = new Container('user');
            $session->offsetUnset('username');
            $session->offsetSet('username',$identity);
            $logged = $session->offsetGet('username');
        }
        
        if ($logged === null): $this->redirect()->toRoute('user', array('action' => 'signin')); endif;
        
        $user = $this->getUserTable()->getUserByName($logged);

        return array(
            'user' => $user,
        );
    }
    
    public function signupAction(){
        $form = new AccountForm();
                   
        $request = $this->getRequest();
        if ($request->isPost()) {
            $user = new User();
            $form->setData($request->getPost());
            
            if ($form->isValid()) {
            	$user->exchangeArray($form->getData());
            	
            	$username = $this->getUserTable()->getUserByName($user->username);
            	
            	if(!empty($username)){
            	    $this->redirect()->toRoute('user', array('action' => 'signup'));
            	    echo '<h3>User already exist</h3>';
            	}else{
            	    $this->getUserTable()->createAccount($user);
            	    $this->redirect()->toRoute('user');
            	}
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
    
    public function accountAction(){
        
        $session = new Container('user');
        $logged = $session->offsetGet('username');
        if($logged === null):$this->redirect()->toRoute('user', array('action' => 'signin'));endif;
        
        $id = (int)$this->params()->fromRoute('id', 0);
        if(!$id):
            return $this->redirect()->toRoute('user');
        endif;
        
        $user = $this->getUserTable()->getUser($id);
        
        $form = new AccountForm();
        $form->bind($user);
        $form->get('submit')->setAttribute('value', 'Save changes');
        $form->get('password')->setAttribute('readonly','true');
        $request = $this->getRequest();
        if ($request->isPost()){
        	$form->setData($request->getPost());
      
        	if ($form->isValid()){
        	    $this->getUserTable()->modifyAccount($user); 
        	    $sm = $this->getServiceLocator();
        	    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
        	     
        	    $authAdapter = new AuthAdapter($dbAdapter);
        	    
        	    $authAdapter->setTableName('user')
        	    ->setIdentityColumn('username')
        	    ->setCredentialColumn('password');
        	     
        	    $authAdapter->setIdentity($user->username)
        	    ->setCredential($user->password);
        	     
        	    $authService = new AuthenticationService();
        	    $authService->setAdapter($authAdapter);
        	    
        	    $result = $authService->authenticate();
        	    return $this->redirect()->toRoute('user');
            }
        }
        
        return array(
            'id' => $id,
            'user' => $user,
            'form' => $form
        );
    }
    
    public function signoutAction(){
        $auth = new AuthenticationService();
        $auth->clearIdentity();
        $session = new Container('user');
        $session->offsetUnset('username');
        return $this->redirect()->toRoute('user');
    }
}