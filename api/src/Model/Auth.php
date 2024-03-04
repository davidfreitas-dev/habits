<?php 

namespace App\Model;

use App\DB\Database;
use App\Model\User;
use App\Utils\ApiResponseFormatter;

class Auth extends User {

  public static function signup($user) 
	{

		$userExists = User::getByEmail($user['email']);
			
		if ($userExists) {

			return ApiResponseFormatter::formatResponse(
        400, 
        "error", 
        "Usuário já cadastrado no banco de dados"
      );

		}
    
    return User::create($user);

	}
        
  public static function signin($user)
  {

    $sql = "SELECT * FROM users           
            WHERE email = :email ";

    try {
      
      $db = new Database();

      $results = $db->select($sql, array(
        ":email"=>$user['email']
      ));

      if (empty($results)) {

        return ApiResponseFormatter::formatResponse(
          404, 
          "error", 
          "Usuário inexistente ou senha inválida."
        );
  
      }

      $data = $results[0];

      if (password_verify($user['password'], $data['password'])) {

        return Auth::generateToken($data);

      } 
      
      return ApiResponseFormatter::formatResponse(
        404, 
        "error", 
        "Usuário inexistente ou senha inválida."
      );

    } catch (\PDOException $e) {
      
      return ApiResponseFormatter::formatResponse(
        500, 
        "error", 
        "Falha na autenticação do usuário: " . $e->getMessage()
      );

    }
    
  }

  private static function generateToken($data)
  {

      $header = [
          'typ' => 'JWT',
          'alg' => 'HS256'
      ];

      $payload = [
          'name' => $data['name'],
          'email' => $data['email'],
      ];

      $header = json_encode($header);
      $payload = json_encode($payload);

      $header = self::base64UrlEncode($header);
      $payload = self::base64UrlEncode($payload);

      $sign = hash_hmac('sha256', $header . "." . $payload, $_ENV['JWT_SECRET_KEY'], true);
      $sign = self::base64UrlEncode($sign);

      $token = $header . '.' . $payload . '.' . $sign;

      $data['token'] = $token;

      return ApiResponseFormatter::formatResponse(200, "success", $data);

  }
  
  private static function base64UrlEncode($data)
  {

    $b64 = base64_encode($data);

    if ($b64 === false) {
      return false;
    }

    $url = strtr($b64, '+/', '-_');

    return rtrim($url, '=');
      
  }

}