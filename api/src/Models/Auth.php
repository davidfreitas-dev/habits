<?php 

namespace App\Models;

use App\DB\Database;
use App\Mail\Mailer;
use App\Models\User;
use App\Utils\PasswordHelper;
use App\Traits\TokenGenerator;
use App\Utils\AESCryptographer;
use App\Utils\ApiResponseFormatter;
use App\Enums\HttpStatus as HTTPStatus;

class Auth {

  use TokenGenerator;

  public static function signup($data) 
	{

		try {

      $user = new User();

      $user->setAttributes($data);

      $data = $user->create();

      $jwt = self::generateToken($data);

      return ApiResponseFormatter::formatResponse(
        HTTPStatus::CREATED, 
        "success", 
        "Usuário cadastrado com sucesso",
        $jwt
      );

    } catch (\Exception $e) {

      return ApiResponseFormatter::formatResponse(
        $e->getCode(), 
        "error", 
        "Falha ao cadastrar usuário: " . $e->getMessage(),
        null
      );

    }

	}
        
  public static function signin($data)
  {

    $sql = "SELECT * FROM users WHERE email = :email";

    try {

      if (!filter_var(strtolower($data['email']), FILTER_VALIDATE_EMAIL)) {

        throw new \Exception("O e-mail informado não é válido.", HTTPStatus::BAD_REQUEST);
        
      }
      
      $db = new Database();

      $results = $db->select($sql, array(
        ":email" => strtolower($data['email']),
      ));

      if (empty($results) || !password_verify($data['password'], $results[0]['password'])) {

        throw new \Exception("Usuário inexistente ou senha inválida.", HTTPStatus::UNAUTHORIZED);
  
      }

      $jwt = self::generateToken($results[0]);

      return ApiResponseFormatter::formatResponse(
        HTTPStatus::OK,
        "success", 
        "Usuário autenticado com sucesso",
        $jwt
      );

    } catch (\PDOException $e) {
      
      return ApiResponseFormatter::formatResponse(
        HTTPStatus::INTERNAL_SERVER_ERROR, 
        "error", 
        "Falha na autenticação do usuário: " . $e->getMessage(),
        NULL
      );

    } catch (\Exception $e) {
      
      return ApiResponseFormatter::formatResponse(
        $e->getCode(), 
        "error", 
        "Falha na autenticação do usuário: " . $e->getMessage(),
        NULL
      );

    }
    
  }

  public static function getForgotToken($email)
  {

    $sql = "SELECT * FROM users WHERE email = :email";

    try {
      
      $db = new Database();

      $results = $db->select($sql, array(
        ":email"=>$email
      ));

      if (empty($results)) {

        throw new \Exception("O e-mail informado não consta no banco de dados", HTTPStatus::NOT_FOUND);

      } 

      $user = $results[0];

      $recoveryId = $db->insert(
        "INSERT INTO users_passwords_recoveries (user_id, ip_address) VALUES (:user_id, :ip_address)", array(
          ":user_id"    => $user['id'],
          ":ip_address" => $_SERVER['REMOTE_ADDR']
        )
      );

      $token = AESCryptographer::encrypt($recoveryId);

      $mailer = new Mailer(
        $user['email'], 
        $user['name'], 
        "Redefinição de senha", 
        array(
          "name" => $user['name'],
          "token" => $token
        )
      );				

      $mailer->send();

      return ApiResponseFormatter::formatResponse(
        HTTPStatus::OK, 
        "success", 
        "Link de redefinição de senha enviado para o e-mail informado.",
        null
      );

    } catch (\PDOException $e) {
      
      return ApiResponseFormatter::formatResponse(
        HTTPStatus::INTERNAL_SERVER_ERROR,
        "error", 
        "Falha ao recuperar senha: " . $e->getMessage(),
        null
      );

    }	catch (\Exception $e) {
      
      return ApiResponseFormatter::formatResponse(
        $e->getCode(), 
        "error", 
        "Falha ao recuperar senha: " . $e->getMessage(),
        null
      );

    }

  }

  public static function validateForgotToken($token)
  {

    if (!isset($token) || empty($token)) {

      throw new \Exception("Falha ao validar token: token inexistente.", HTTPStatus::UNAUTHORIZED);

    }

    $recoveryId = AESCryptographer::decrypt($token);

    $sql = "SELECT * FROM users_passwords_recoveries AS upr
            INNER JOIN users AS u 
            ON upr.user_id = u.id
            WHERE upr.id = :recovery_id
            AND upr.recovery_date IS NULL
            AND DATE_ADD(upr.created_at, INTERVAL 1 HOUR) >= NOW()";
    
    try {
      
      $db = new Database();

      $results = $db->select($sql, array(
        ":recovery_id" => $recoveryId
      ));

      if (empty($results)) {

        throw new \Exception("O token de redefinição utilizado expirou", HTTPStatus::UNAUTHORIZED);

      } 

      return ApiResponseFormatter::formatResponse(
        HTTPStatus::OK, 
        "success", 
        "Link de redefinição validado com sucesso.",
        array (
          "recoveryId" => $recoveryId,
          "userId"     => $results[0]['user_id'],
        )
      );

    } catch (\PDOException $e) {
      
      return ApiResponseFormatter::formatResponse(
        HTTPStatus::INTERNAL_SERVER_ERROR, 
        "error", 
        "Falha ao validar token: " . $e->getMessage(),
        null
      );

    } catch (\Exception $e) {
      
      return ApiResponseFormatter::formatResponse(
        $e->getCode(), 
        "error", 
        $e->getMessage(),
        null
      );

    }

  }

  public static function setNewPassword($data)
  {

    $sql = "UPDATE users 
            SET password = :password 
            WHERE id = :user_id";

    try {

      PasswordHelper::checkPasswordStrength($data['password']);

      $db = new Database();

      $db->query($sql, array(
        ":user_id"  => $data['userId'],
        ":password" => PasswordHelper::hashPassword($data['password'])
      ));

      if (isset($data['recoveryId'])) {
        
        self::setForgotUsed($data['recoveryId']);

      }

      return ApiResponseFormatter::formatResponse(
        HTTPStatus::OK, 
        "success", 
        "Senha alterada com sucesso.",
        NULL
      );

    } catch (\PDOException $e) {

      return ApiResponseFormatter::formatResponse(
        HTTPStatus::INTERNAL_SERVER_ERROR, 
        "error", 
        "Falha ao gravar nova senha: " . $e->getMessage(),
        NULL
      );

    } catch (\Exception $e) {

      return ApiResponseFormatter::formatResponse(
        $e->getCode(), 
        "error", 
        "Falha ao gravar nova senha: " . $e->getMessage(),
        NULL
      );

    }

  }

  private static function setForgotUsed($recoveryId)
  {

    $sql = "UPDATE users_passwords_recoveries 
            SET recovery_date = NOW() 
            WHERE id = :recovery_id";

    try {

      $db = new Database();

      $db->query($sql, array(
        ":recovery_id" => $recoveryId
      ));

    } catch (\PDOException $e) {

      return ApiResponseFormatter::formatResponse(
        HTTPStatus::INTERNAL_SERVER_ERROR, 
        "error", 
        "Falha ao definir senha antiga como usada: " . $e->getMessage(),
        NULL
      );

    }

  }

}