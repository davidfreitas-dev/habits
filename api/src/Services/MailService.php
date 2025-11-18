<?php

namespace App\Services;

use App\Interfaces\MailerInterface;

class MailService
{
  
  private MailerInterface $mailer;

  public function __construct(MailerInterface $mailer)
  {
      
    $this->mailer = $mailer;
    
  }
  
  public function sendPasswordReset(string $toEmail, string $toName, string $otpCode): bool
  {
      
    $subject = "Redefinição de senha";
      
    $content = "
      <p>Olá <strong>{$toName}</strong>,</p>
      <p>Recebemos uma solicitação para redefinir sua senha. Use o código abaixo para continuar o processo no aplicativo:</p>

      <div style='text-align:center; margin:30px 0;'>
        <div style='display:inline-block; padding:16px 32px; background:#fff; color:#16a34a; border-radius:12px; font-size:24px; font-weight:bold; letter-spacing:4px;'>
          {$otpCode}
        </div>
      </div>

      <p>Copie o código acima e insira no app para concluir a redefinição da sua senha.</p>
      <p>Se você não solicitou essa alteração, pode ignorar este e-mail.</p>
    ";

    return $this->mailer->send($toEmail, $toName, $subject, $content);
  
  }
  
  public function sendSignupConfirmation(string $toEmail, string $toName, string $welcomeLink): bool
  {

    $subject = "Bem-vindo ao {$_ENV['APP_NAME']}!";
    
    $content = "
      <p>Seu cadastro foi realizado com sucesso!</p>
      <p>Agora você já pode abrir o aplicativo e acessar sua conta normalmente.</p>
      <br>
      <p>Obrigado por se juntar ao {$_ENV['APP_NAME']}.</p>
    ";
    
    return $this->mailer->send($toEmail, $toName, $subject, $content);
  
  }

}
