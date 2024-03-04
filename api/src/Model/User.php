<?php 

namespace App\Model;

use App\DB\Database;
use App\Utils\ApiResponseFormatter;

class User {
  
  public static function create($user)
  {

    $sql = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";

    try {
      
      $db = new Database();

			$results = $db->select($sql, array(
				":name"=>$user['name'],
				":email"=>$user['email'],
				":password"=>User::getPasswordHash($user['password'])
			));

      return ApiResponseFormatter::formatResponse(
        201, 
        "success", 
        "Cadastro efetuado com sucesso"
      );

    } catch (\PDOException $e) {
			
			return ApiResponseFormatter::formatResponse(
        500, 
        "error", 
        "Falha ao efetuar cadastro: " . $e->getMessage()
      );
			
		}		

  }

	public static function get($userId)
	{

		$sql = "SELECT * FROM users 
            WHERE user_id = :userId";

		try {

			$db = new Database();

			$results = $db->select($sql, array(
				":userId"=>$userId
			));

      if (count($results)) {
			
			  return ApiResponseFormatter::formatResponse(
          200, 
          "success", 
          $results[0]
        );
        
      }

			return ApiResponseFormatter::formatResponse(
        404, 
        "error", 
        "Usuário não encontrado"
      );

		} catch (\PDOException $e) {
			
			return ApiResponseFormatter::formatResponse(
        500, 
        "error", 
        "Falha ao obter usuário: " . $e->getMessage()
      );
			
		}

	}

  public static function getByEmail($email) 
	{
		
		$sql = "SELECT * FROM users 
            WHERE email = :email";
		
		try {

			$db = new Database();
			
			$results = $db->select($sql, array(
				":email"=>$email
			));

			return count($results);

		} catch (\PDOException $e) {

			return ApiResponseFormatter::formatResponse(
        500, 
        "error", 
        "Falha ao obter usuário: " . $e->getMessage()
      );

		}		

	}

  private static function getPasswordHash($password)
	{

		return password_hash($password, PASSWORD_BCRYPT, [
			'cost' => 12
		]);

	}

}

 ?>