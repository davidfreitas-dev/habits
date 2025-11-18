<?php

namespace App\Utils;

class ApiResponse
{

  public static function success(string $message = 'OK', $data = NULL, int $code = 200)
  {

    $response = [
      'success' => true,
      'message' => $message,
      'code'    => $code
    ];

    if (!is_null($data)) {
        
      $response['data'] = $data;
      
    }

    return $response;

  }

  public static function error(string $message, int $code)
  {

    return [
      'success' => false,
      'message' => $message,
      'code'    => $code
    ];

  }

}
