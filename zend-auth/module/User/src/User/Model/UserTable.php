<?php

namespace User\Model;

use Zend\Db\TableGateway\TableGateway;

class UserTable {
	
	protected $tableGateway;
	
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function getUser($id)
	{
		$id  = (int) $id;
		$rowset = $this->tableGateway->select(array(
				'id' => $id
		));
		$row = $rowset->current();
		if (!$row) {
			throw new \Exception("Could not find row $id");
		}
		return $row;
	}
	
	public function createAccount(User $user)
	{
		$data = array(
		    'username' => $user->username,
		    'password'  => sha1($user->password)
		);
        $this->tableGateway->insert($data);
	}
}