<?php

namespace App\Model;

use App\DB\Database;
use App\Utils\ApiResponseFormatter;

class Habit {

  public static function create($payload) 
  {

    try {
      
      $db = new Database();

      $db->query("CALL sp_habits_create(:title, :weekDays, :userId)", array(
        ":title"=>$payload['title'],
        ":weekDays"=>$payload['weekDays'],
        ":userId"=>$payload['userId']
      ));

      return ApiResponseFormatter::formatResponse(
        201, 
        "success", 
        "Hábito criado com sucesso"
      );

    } catch (\PDOException $e) {

      return ApiResponseFormatter::formatResponse(
        500, 
        "error", 
        "Erro ao criar hábito: " . $e->getMessage()
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

      if (count($results)) {

				return ApiResponseFormatter::formatResponse(
          200, 
          "success", 
          $results
        );

			} 
      
      return ApiResponseFormatter::formatResponse(
        204, 
        "success", 
        "Resumo não disponível para o dia selecionado"
      );

    } catch (\PDOException $e) {

      return ApiResponseFormatter::formatResponse(
        500, 
        "error", 
        "Erro ao obter resumo do dia: " . $e->getMessage()
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
      200, 
      "success", 
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
        201, 
        "success", 
        "Hábito marcado/desmarcado com sucesso"
      );

    } catch (\PDOException $e) {

      return ApiResponseFormatter::formatResponse(
        500, 
        "error", 
        "Erro ao marcar/desmarcar hábito: " . $e->getMessage()
      );

    }

  }

}