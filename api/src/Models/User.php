<?php 

namespace App\Models;

use App\DB\Database;
use App\Models\Model;
use App\Utils\PasswordHelper;
use App\Enums\HttpStatus as HTTPStatus;

class User extends Model {
  
  public function __construct(Database $db)
  {
    
    $this->db = $db; // Já existe na classe Model
    
    parent::__construct($db);
    
  }
  
  public function create()
  {

    $this->checkUserExists($this->getEmail());

    PasswordHelper::checkPasswordStrength($this->getPassword());

    $hashedPassword = PasswordHelper::hashPassword($this->getPassword());

    $userId = $this->db->insert(
      "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)",
      [
        ":name"     => $this->getName(),
        ":email"    => $this->getEmail(),
        ":password" => $hashedPassword
      ]
    );

    $this->setId($userId);
    
    $this->setPassword($hashedPassword);

    return $this->getAttributes();

  }

  public function update()
  {

    $email = strtolower(trim($this->getEmail()));
      
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          
      throw new \Exception("E-mail inválido.", HTTPStatus::BAD_REQUEST);
      
    }

    $this->checkUserExists($email, $this->getId());

    $this->db->query(
      "UPDATE users SET name = :name, email = :email WHERE id = :id", 
      [
        ":id"       => $this->getId(),
        ":name"     => $this->getName(),
        ":email"    => $email
      ]
    );

    return $this->getAttributes();

  }

  public function get($id)
  {

    $results = $this->db->select(
      "SELECT id, name, email FROM users WHERE id = :id", 
      [
        ":id" => $id
      ]
    );

    if (empty($results)) {
      
      throw new \Exception("Usuário não encontrado.", HTTPStatus::NOT_FOUND);

    }

    return $results[0];

  }

  public function delete($id) 
	{	
		
		$affectedRows = $this->db->query(
      "DELETE FROM users WHERE id = :id", 
      [
        ":id" => $id
      ]
    );

    if ($affectedRows === 0) {
        
      throw new \Exception("Usuário não encontrado.", HTTPStatus::NOT_FOUND);
      
    }

	}

  private function checkUserExists($email, $id = NULL) 
  {

    $sql = "SELECT * FROM users WHERE email = :email";

    if ($id) {

      $sql .= " AND id != :id";

    }

    $params = [
      ":email" => $email
    ];

    if ($id) {

      $params[":id"] = $id;

    }
      
    $results = $this->db->select($sql, $params);

    if (count($results) > 0) {
      
      throw new \Exception("O email informado já está sendo utilizado por outro usuário", HTTPStatus::BAD_REQUEST);

    }

  }

}

 ?>