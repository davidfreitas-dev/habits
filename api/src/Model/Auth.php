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
    
    $results = User::create($user);

    if ($results['status'] == 'error') {

      return $results;

    }

    $data = $results['data'];

    return Auth::generateToken($data);

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

  public static function getForgotToken($email)
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

  public static function validateForgotToken($token)
  {

    $recoveryId = AESCryptographer::decrypt($token);

    $sql = "SELECT * FROM users_passwords_recoveries AS upr
            INNER JOIN users AS u 
            ON upr.user_id = u.id
            WHERE upr.id = :recoveryId
            AND upr.recovery_date IS NULL
            AND DATE_ADD(upr.created_at, INTERVAL 1 HOUR) >= NOW()";
    
    try {
      
      $db = new Database();

      $results = $db->select($sql, array(
        ":recoveryId"=>$recoveryId
      ));

      if (empty($results)) {

        return ApiResponseFormatter::formatResponse(
          401, 
          "error", 
          "O token de redefinição utilizado expirou"
        );

      }

      return ApiResponseFormatter::formatResponse(
        200, 
        "success", 
        array (
          "recoveryId"=>$recoveryId,
          "userId"=>$results[0]['user_id'],
        )
      );

    } catch (\PDOException $e) {
      
      return ApiResponseFormatter::formatResponse(
        500, 
        "error", 
        "Falha ao validar token: " . $e->getMessage()
      );

    }

  }

  public static function setNewPassword($payload)
  {

    $sql = "UPDATE users 
            SET password = :password 
            WHERE id = :userId";

    try {

      $db = new Database();

      $db->query($sql, array(
        ":userId"=>$payload['userId'],
        ":password"=>Auth::getPasswordHash($payload['password']),
      ));

      Auth::setForgotUsed($payload['recoveryId']);

      return ApiResponseFormatter::formatResponse(
        200, 
        "success", 
        "Senha alterada com sucesso"
      );

    } catch (\PDOException $e) {

      return ApiResponseFormatter::formatResponse(
        500, 
        "error", 
        "Falha ao gravar nova senha: " . $e->getMessage()
      );

    }

  }

  private static function setForgotUsed($recoveryId)
  {

    $sql = "UPDATE users_passwords_recoveries 
            SET recovery_date = NOW() 
            WHERE id = :recoveryId";

    try {

      $db = new Database();

      $db->query($sql, array(
        ":recoveryId"=>$recoveryId
      ));

    } catch (\PDOException $e) {

      return ApiResponseFormatter::formatResponse(
        500, 
        "error", 
        "Falha ao definir senha antiga como usada: " . $e->getMessage()
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