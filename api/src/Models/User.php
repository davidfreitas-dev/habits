<?php 

namespace App\Models;

use App\DB\Database;
use App\Models\Model;
use App\Utils\PasswordHelper;
use App\Traits\TokenGenerator;
use App\Enums\HttpStatus as HTTPStatus;

class User extends Model {

  use TokenGenerator;
  
  public function create()
  {

    $sql = "INSERT INTO users (`name`, `email`, `password`) VALUES (:name, :email, :password)";

    try {

      if (!filter_var(strtolower($this->getemail()), FILTER_VALIDATE_EMAIL)) {

        throw new \Exception("O e-mail informado não é válido.", HTTPStatus::BAD_REQUEST);
        
      }

      PasswordHelper::checkPasswordStrength($this->getpassword());

      $this->checkUserExists(strtolower($this->getemail()));
      
      $db = new Database();

			$userId = $db->insert($sql, array(
				":name"     => $this->getname(),
				":email"    => strtolower($this->getemail()),
				":password" => PasswordHelper::hashPassword($this->getpassword())
			));

      $this->setId($userId);
      
      $this->setPassword(PasswordHelper::hashPassword($this->getpassword()));

      return $this->getAttributes();

    } catch (\PDOException $e) {
			
			throw new \Exception($e->getMessage(), HTTPStatus::INTERNAL_SERVER_ERROR);
			
		}	catch (\Exception $e) {
			
			throw new \Exception($e->getMessage(), $e->getCode());
			
		}			

  }

  private function checkUserExists($email, $id = NULL) 
  {

    $sql = "SELECT * FROM users WHERE email = :email";

    if ($id) {

      $sql .= " AND id != :id";

    }

    try {

      $db = new Database();

      $params = [
        ":email" => $email
      ];

      if ($id) {

        $params[":id"] = $id;
  
      }
        
      $results = $db->select($sql, $params);

      if (count($results) > 0) {
        
        throw new \Exception("O email informado já está cadastrado", HTTPStatus::BAD_REQUEST);

      }

    } catch (\PDOException $e) {

      throw new \Exception($e->getMessage(), HTTPStatus::INTERNAL_SERVER_ERROR);
      
    } catch (\Exception $e) {

      throw new \Exception($e->getMessage(), $e->getCode());
      
    }

  }

}

 ?>