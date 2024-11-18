<?php

namespace App\Models;

use App\DB\Database;
use App\Models\Model;
use App\Enums\HttpStatus as HTTPStatus;
use App\Utils\ApiResponseFormatter;

class Habit extends Model {

  public function create() 
  {

    try {

      $this->checkHabitExists($this->getTitle());
      
      $db = new Database();

      $db->query("CALL sp_habits_create(:title, :week_days, :user_id)", array(
        ":title"     => $this->getTitle(),
        ":week_days" => $this->getWeekDays(),
        ":user_id"   => $this->getUserId()
      ));

      return ApiResponseFormatter::formatResponse(
        HTTPStatus::CREATED, 
        "success", 
        "Hábito criado com sucesso",
        $this->getAttributes()
      );

    } catch (\PDOException $e) {

      return ApiResponseFormatter::formatResponse(
        HTTPStatus::INTERNAL_SERVER_ERROR, 
        "error", 
        "Erro ao criar hábito: " . $e->getMessage(),
        NULL
      );

    } catch (\Exception $e) {

      return ApiResponseFormatter::formatResponse(
        $e->getCode(), 
        "error", 
        $e->getMessage(),
        NULL
      );

    }

  }

  public function update() 
  {

    try {

      $this->checkHabitExists($this->getTitle(), $this->getId());

      $db = new Database();

      $db->query("CALL sp_habits_update(:id, :title, :week_days)", array(
        ":id"        => $this->getId(),
        ":title"     => $this->getTitle(),
        ":week_days" => $this->getWeekDays()
      ));

      return ApiResponseFormatter::formatResponse(
        HTTPStatus::OK,
        "success",
        "Hábito atualizado com sucesso",
        $this->getAttributes()
      );

    } catch (\PDOException $e) {
      
      return ApiResponseFormatter::formatResponse(
        HTTPStatus::INTERNAL_SERVER_ERROR,
        "error",
        "Erro ao atualizar hábito: " . $e->getMessage(),
        NULL
      );

    } catch (\Exception $e) {
      
      return ApiResponseFormatter::formatResponse(
        $e->getCode(),
        "error",
        $e->getMessage(),
        NULL
      );
      
    }

  }

  public static function summary($userId) 
	{

    $sql = "SELECT
        D.id,
        D.date,
        (
          SELECT
            COUNT(*)
          FROM day_habits DH
          JOIN habits H1 ON DH.habit_id = H1.id
          WHERE DH.day_id = D.id
            AND H1.user_id = :userId
        ) AS completed,
        (
          SELECT 
            COUNT(*)
          FROM habit_week_days HWD
          JOIN habits H2 ON HWD.habit_id = H2.id
          WHERE WEEKDAY(D.date) = HWD.week_day
            AND H2.created_at <= D.date
            AND H2.user_id = :userId
        ) AS amount
      FROM days D;
    ";

    try {
      
      $db = new Database();

      $results = $db->select($sql, array(
        ":userId"=>$userId
      ));

      if (empty($results)) {

        return ApiResponseFormatter::formatResponse(
          HTTPStatus::NO_CONTENT, 
          "success", 
          "Resumo não disponível para o dia selecionado",
          NULL
        );				

			} 

      return ApiResponseFormatter::formatResponse(
        HTTPStatus::OK,  
        "success", 
        "Resumo dos hábitos para o dia selecionado",
        $results
      );      

    } catch (\PDOException $e) {

      return ApiResponseFormatter::formatResponse(
        HTTPStatus::INTERNAL_SERVER_ERROR, 
        "error", 
        "Erro ao obter resumo para o dia selecionado: " . $e->getMessage(),
        NULL
      );

    }

  }

  public static function list($payload) 
  {

    $date = $payload['date'];

    $userId = $payload['userId'];

    $possibleHabits = Habit::getPossibleHabits($date, $userId);

    $completedHabits = Habit::getCompletedHabits($date, $userId);

    return ApiResponseFormatter::formatResponse(
      HTTPStatus::OK,  
      "success", 
      "Lista de hábitos possíveis e completados",
      [
        "possibleHabits" => $possibleHabits,
        "completedHabits" => $completedHabits
      ]
    );

  }

  private static function getPossibleHabits($date, $userId) 
	{

    $weekDay = date('w', strtotime($date));
    
    $possibleHabitsQuery = "
      SELECT *
      FROM habits
      WHERE created_at <= :date
        AND id IN (
          SELECT habit_id
          FROM habit_week_days
          WHERE week_day = :weekDay
        )
        AND user_id = :userId
    ";

    try {
      
      $db = new Database();

      $results = $db->select($possibleHabitsQuery, array(
        ":date"=>$date,
        ":weekDay"=>$weekDay,
        ":userId"=>$userId
      ));

      return $results;

    } catch (\PDOException $e) {

      return array(
        "message" => $e->getMessage()
      );

    }

  }

  private static function getCompletedHabits($date, $userId) 
	{

    $formattedDate = date('Y-m-d', strtotime($date));

    $completedHabitsQuery = "
      SELECT habit_id
      FROM day_habits
      WHERE day_id = (
        SELECT id
        FROM days
        WHERE date = :date
      )
      AND habit_id IN (
        SELECT id
        FROM habits
        WHERE user_id = :userId
      )
    ";


    try {
      
      $db = new Database();

      $results = $db->select($completedHabitsQuery, array(
        ":date"=>$formattedDate,
        ":userId"=>$userId
      ));

      return $results;

    } catch (\PDOException $e) {

      return array(
        "message" => $e->getMessage()
      );

    }

  }

  public static function toggle($userId, $habitId) 
  {

    date_default_timezone_set('America/Sao_Paulo');

    $currentDate = date('Y-m-d H:i:s', strtotime('today'));

    try {
      
      $db = new Database();

      $db->query("CALL sp_habits_toggle(:userId, :habitId, :currentDate)", array(
        ":userId"=>$userId,
        ":habitId"=>$habitId,
        ":currentDate"=>$currentDate
      ));

      return ApiResponseFormatter::formatResponse(
        HTTPStatus::CREATED, 
        "success", 
        "Hábito marcado/desmarcado com sucesso",
        NULL
      );

    } catch (\PDOException $e) {

      return ApiResponseFormatter::formatResponse(
        HTTPStatus::INTERNAL_SERVER_ERROR, 
        "error", 
        "Erro ao marcar/desmarcar hábito: " . $e->getMessage(),
        NULL
      );

    }

  }

  private function checkHabitExists($title, $id = NULL) 
  {

    $sql = "SELECT * FROM habits WHERE title = :title";

    if ($id) {

      $sql .= " AND id != :id";

    }

    try {

      $db = new Database();

      $params = [
        ":title" => $title
      ];

      if ($id) {

        $params[":id"] = $id;
  
      }
        
      $results = $db->select($sql, $params);

      if (count($results) > 0) {
        
        throw new \Exception("Já existe um hábito com este título.", HTTPStatus::BAD_REQUEST);

      }

    } catch (\PDOException $e) {

      throw new \Exception($e->getMessage(), HTTPStatus::INTERNAL_SERVER_ERROR);
      
    } catch (\Exception $e) {

      throw new \Exception($e->getMessage(), $e->getCode());
      
    }

  }

}