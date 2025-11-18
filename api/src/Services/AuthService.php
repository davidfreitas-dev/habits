<?php

namespace App\Services;

use App\Models\User;
use App\DB\Database;
use App\Utils\PasswordHelper;
use App\Utils\AESCryptographer;
use App\Services\TokenService;
use App\Enums\HttpStatus as HTTPStatus;

class AuthService
{

  private Database $db;
  
  private $tokenService;

  public function __construct(Database $db, TokenService $tokenService)
  {
      
    $this->db = $db;
      
    $this->tokenService = $tokenService;

  }

  public function signup(array $data): string
	{

		$requiredFields = ["name", "email", "password"];
      
    foreach ($requiredFields as $field) {
      
      if (!isset($data[$field]) || trim($data[$field]) === "") {
        
        throw new \Exception("O campo '$field' é obrigatório.", HTTPStatus::BAD_REQUEST);
      
      }

    }

    $email = strtolower(trim($data['email']));
      
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          
      throw new \Exception("O e-mail informado não é válido.", HTTPStatus::BAD_REQUEST);
      
    }

    $user = new User($this->db);

    $user->setAttributes($data);

    $data = $user->create();

    unset($data['password']);

    $jwt = $this->tokenService->generateTokenForUser($data);

    return $jwt;

	}
        
  public function signin(string $email, string $password): string
  {

    $email = strtolower(trim($email));
      
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          
      throw new \Exception("O e-mail informado não é válido.", HTTPStatus::BAD_REQUEST);
      
    }

    $results = $this->db->select(
      "SELECT * FROM users WHERE email = :login", 
      array(
        ":login" => $email
      )
    );

    $user = $results[0] ?? NULL;

    if (empty($results) || !password_verify($password, $user['password'])) {
          
      throw new \Exception("Usuário inexistente ou senha inválida.", HTTPStatus::UNAUTHORIZED);
      
    }

    unset($user['password']);

    $jwt = $this->tokenService->generateTokenForUser($user);

    return $jwt;
  
  }

  public function requestPasswordReset(string $email): array
  {
    
    $results = $this->db->select(
      "SELECT * FROM users WHERE email = :email", 
      array(
        ":email" => strtolower(trim($email))
      )
    );

    if (empty($results)) {
        
      throw new \Exception("Não foi possível encontrar um usuário com esse e-mail.", HTTPStatus::NOT_FOUND);
    
    }

    $user = $results[0] ?? NULL;

    $recoveryId = $this->db->insert(
      "INSERT INTO users_passwords_recoveries (user_id, ip_address) VALUES (:user_id, :ip_address)", [
        ":user_id"    => $user['id'],
        ":ip_address" => $_SERVER['REMOTE_ADDR']
      ]
    );

    $code = AESCryptographer::encrypt([
      "recovery_id" => $recoveryId,
      "user_id"     => $user["id"]
    ]);

    return [
      "code"        => $code,
      "user"        => $user,
      "id_recovery" => $recoveryId
    ];

  }

  public function verifyResetToken(string $code)
  {

    if (empty($code)) {
        
      throw new \Exception("Falha ao validar token: token inexistente.", HTTPStatus::UNAUTHORIZED);
      
    }
    
    $decryptedData = AESCryptographer::decrypt($code);

    if (!is_array($decryptedData) || !isset($decryptedData["recovery_id"], $decryptedData["user_id"])) {
      
      throw new \Exception("Token inválido ou corrompido.", 401);
    
    }
    
    $recoveryId = $decryptedData["recovery_id"];

    $sql = "SELECT * FROM users_passwords_recoveries AS upr
            INNER JOIN users AS u 
            ON upr.user_id = u.id
            WHERE upr.id = :id
            AND upr.recovery_date IS NULL
            AND DATE_ADD(upr.created_at, INTERVAL 1 HOUR) >= NOW()";

    $results = $this->db->select($sql, array(
      ":id" => $recoveryId
    ));

    if (empty($results)) {
      
      throw new \Exception("O link de redefinição utilizado expirou.", 401);
    
    }

    return [
      "user_id"     => $results[0]["user_id"],
      "recovery_id" => $results[0]["id"]
    ];
  
  }

  public function resetPassword(string $password, array $data)
  {
    
    PasswordHelper::checkPasswordStrength($password);

    $sql = "UPDATE users SET password = :password WHERE id = :user_id";
    
    $this->db->query($sql, [
      ":password" => PasswordHelper::hashPassword($password),
      ":user_id"  => $data["user_id"]
    ]);

    self::setForgotUsed($data["recovery_id"]);

    return true;
  
  }

  private function setForgotUsed(int $recoveryId)
  {
    
    $sql = "UPDATE users_passwords_recoveries 
            SET recovery_date = NOW() 
            WHERE id = :id";
    
    $this->db->query($sql, array(
      ":id" => $recoveryId
    ));
  
  }

}
