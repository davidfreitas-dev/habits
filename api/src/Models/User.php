<?php 

namespace App\Models;

use App\DB\Database;
use App\Models\Model;
use App\Utils\PasswordHelper;
use App\Traits\TokenGenerator;
use App\Utils\ApiResponseFormatter;
use App\Enums\HttpStatus as HTTPStatus;

class User extends Model {

  use TokenGenerator;
  
  public function create()
  {

    $sql = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";

    try {

      if (!filter_var(strtolower($this->getEmail()), FILTER_VALIDATE_EMAIL)) {

        throw new \Exception("O e-mail informado não é válido.", HTTPStatus::BAD_REQUEST);
        
      }

      PasswordHelper::checkPasswordStrength($this->getPassword());

      $this->checkUserExists(strtolower($this->getEmail()));
      
      $db = new Database();

			$userId = $db->insert($sql, array(
				":name"     => $this->getName(),
				":email"    => strtolower($this->getEmail()),
				":password" => PasswordHelper::hashPassword($this->getPassword())
			));

      $this->setId($userId);
      
      $this->setPassword(PasswordHelper::hashPassword($this->getPassword()));

      return $this->getAttributes();

    } catch (\PDOException $e) {
			
			throw new \Exception($e->getMessage(), HTTPStatus::INTERNAL_SERVER_ERROR);
			
		}	catch (\Exception $e) {
			
			throw new \Exception($e->getMessage(), $e->getCode());
			
		}			

  }

  public function update()
  {

    $sql = "UPDATE users 
            SET name = :name, email = :email
            WHERE id = :id";

    try {

        if (!filter_var(strtolower($this->getEmail()), FILTER_VALIDATE_EMAIL)) {
            
          throw new \Exception("O e-mail informado não é válido.", HTTPStatus::BAD_REQUEST);
          
        }

        $this->checkUserExists(strtolower($this->getEmail()), $this->getId());

        $db = new Database();

        $db->query($sql, array(
          ":id"       => $this->getId(),
          ":name"     => $this->getName(),
          ":email"    => strtolower($this->getEmail())
        ));

        $jwt = self::generateToken($this->getAttributes());

        return ApiResponseFormatter::formatResponse(
          HTTPStatus::OK, 
          "success", 
          "Usuário atualizado com sucesso",
          $jwt
        );

    } catch (\PDOException $e) {
        
      return ApiResponseFormatter::formatResponse(
        HTTPStatus::INTERNAL_SERVER_ERROR, 
        "error", 
        "Falha ao atualizar dados do usuário: " . $e->getMessage(),
        NULL
      );

    } catch (\Exception $e) {
        
      return ApiResponseFormatter::formatResponse(
        $e->getCode(), 
        "error", 
        $e->getMessage(),
        NULL
      );

    }

  }

  public static function get($id)
  {

    $sql = "SELECT * FROM users WHERE id = :id";

    try {
      
      $db = new Database();

      $results = $db->select($sql, array(
        ":id" => $id
      ));

      if (empty($results)) {
        
        return ApiResponseFormatter::formatResponse(
          HTTPStatus::NOT_FOUND, 
          "error", 
          "Usuário não encontrado.",
          NULL
        );

      }

      return ApiResponseFormatter::formatResponse(
        HTTPStatus::OK,
        "success", 
        "Dados do usuário",
        $results[0]
      );

    } catch (\PDOException $e) {
      
      return ApiResponseFormatter::formatResponse(
        HTTPStatus::INTERNAL_SERVER_ERROR, 
        "error", 
        "Falha ao obter dados do usuário: " . $e->getMessage(),
        NULL
      );

    }
  }

  public static function delete($id) 
	{
		
		$sql = "DELETE FROM users WHERE id = :id";		
		
		try {

			$db = new Database();
			
			$db->query($sql, array(
				":id" => $id
			));
			
			return ApiResponseFormatter::formatResponse(
        HTTPStatus::OK, 
        "success", 
        "Usuário excluído com sucesso",
        null
      );

		} catch (\PDOException $e) {

			return ApiResponseFormatter::formatResponse(
        HTTPStatus::INTERNAL_SERVER_ERROR, 
        "error", 
        "Falha ao excluir usuário: " . $e->getMessage(),
        null
      );
			
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
        
        throw new \Exception("O email informado já está sendo utilizado por outro usuário", HTTPStatus::BAD_REQUEST);

      }

    } catch (\PDOException $e) {

      throw new \Exception($e->getMessage(), HTTPStatus::INTERNAL_SERVER_ERROR);
      
    } catch (\Exception $e) {

      throw new \Exception($e->getMessage(), $e->getCode());
      
    }

  }

}

 ?>