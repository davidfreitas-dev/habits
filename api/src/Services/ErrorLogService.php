<?php

namespace App\Services;

use Throwable;
use App\DB\Database;

class ErrorLogService
{
  public function __construct(private Database $db) {}

  public function log(Throwable $e, array $context = []): void
  {
    
    try {
      
      $this->db->insert(
        "INSERT INTO error_logs (level, message, trace, context)
          VALUES (:level, :message, :trace, :context)",
        [
          ":level"   => "ERROR",
          ":message" => $e->getMessage(),
          ":trace"   => $e->getTraceAsString(),
          ":context" => json_encode(array_merge([
            "file" => $e->getFile(),
            "line" => $e->getLine(),
            "code" => $e->getCode()
          ], $context))
        ]
      );
    
    } catch (Throwable $loggingError) {
      
      error_log("Falha ao gravar log no banco: " . $loggingError->getMessage());
      
    }
  
  }

}
