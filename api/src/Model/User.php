<?php 

namespace App\Model;

use App\DB\Database;
use App\Utils\ApiResponseFormatter;

class User {
  
  public static function create($user)
  {

    $sql = "CALL sp_users_create(:name, :email, :password)";

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
        $results[0]
      );

    } catch (\PDOException $e) {
			
			return ApiResponseFormatter::formatResponse(
        500, 
        "error", 
        "Falha ao efetuar cadastro: " . $e->getMessage()
      );
			
		}		

  }

	public static function get($credential)
	{

		$sql = "SELECT * FROM users 
            WHERE id = :credential
            OR email = :credential";

		try {

			$db = new Database();

			$results = $db->select($sql, array(
				":credential"=>$credential
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

  public static function getPasswordHash($password)
	{

		return password_hash($password, PASSWORD_BCRYPT, [
			'cost' => 12
		]);

	}

}

 ?>