<?php

namespace App\Models;

use \PDO;

class DB {

	private $conn;

	public function __construct()
	{

		$this->conn = new \PDO(
			"mysql:dbname=".$_ENV['DBNAME'].";host=".$_ENV['HOST'], 
			$_ENV['USERNAME'],
			$_ENV['PASSWORD'], 
			array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8;SET time_zone='America/Sao_Paulo'")
		);

	}

  private function setParams($statement, $parameters = array())
	{

		foreach ($parameters as $key => $value) {
			
			$this->bindParam($statement, $key, $value);

		}

	}

	private function bindParam($statement, $key, $value)
	{

		$statement->bindParam($key, $value);

	}

	public function query($rawQuery, $params = array())
	{

		$stmt = $this->conn->prepare($rawQuery);

		$this->setParams($stmt, $params);

		$stmt->execute();

	}

	public function select($rawQuery, $params = array()):array
	{

		$stmt = $this->conn->prepare($rawQuery);

		$this->setParams($stmt, $params);

		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);

	}
    
}
