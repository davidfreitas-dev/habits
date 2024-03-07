<?php 

namespace App\Model;

use App\DB\Database;
use App\Mail\Mailer;
use App\Utils\AESCryptographer;
use App\Utils\ApiResponseFormatter;

class Auth extends User {

  public static function signup($user) 
	{

		$results = User::get($user['email']);
			
		if ($results['status'] == 'success') {

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

    $results = User::get($user['email']);

    if ($results['status'] == 'error') {

      return ApiResponseFormatter::formatResponse(
        404, 
        "error", 
        "Usuário inexistente ou senha inválida."
      );

    }

    $data = $results['data'];

    if (!password_verify($user['password'], $data['password'])) {

      return ApiResponseFormatter::formatResponse(
        404, 
        "error", 
        "Usuário inexistente ou senha inválida."
      );

    }

    return Auth::generateToken($data);
    
  }

  public static function getForgotLink($email)
  {

    $result = User::get($email);
      
    if ($result['status'] == 'error') {

      return ApiResponseFormatter::formatResponse(
        404, 
        "error", 
        "O e-mail informado não consta no banco de dados"
      );

    } 

    $user = $result['data'];

    try {
      
      $db = new Database();

      $results = $db->select(
        "CALL sp_users_passwords_recoveries_create(:userId, :ip)", array(
          ":userId"=>$user['id'],
          ":ip"=>$_SERVER['REMOTE_ADDR']
        )
      ); 

      if (empty($results))	{

        return ApiResponseFormatter::formatResponse(
          400, 
          "error", 
          "Não foi possível recuperar a senha"
        );

      }

      $recovery = $results[0];

      $token = AESCryptographer::encrypt($recovery['id']);

      $mailer = new Mailer(
        $user['email'], 
        $user['name'], 
        "Redefinição de senha", 
        array(
          "name"=>$user['name'],
          "token"=>$token
        )
      );

      $mailer->send();

      return ApiResponseFormatter::formatResponse(
        200, 
        "success", 
        "Token de redefinição de senha enviado para o e-mail informado"
      );

    } catch (\PDOException $e) {
      
      return ApiResponseFormatter::formatResponse(
        500, 
        "error", 
        "Falha ao recuperar senha: " . $e->getMessage()
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