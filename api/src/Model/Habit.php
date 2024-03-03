<?php

namespace App\Model;

use App\DB\Database;
use App\Utils\ApiResponseFormatter;

class Habit {

  public static function list($date) 
  {

    $possibleHabits = Habit::getPossibleHabits($date);

    $completedHabits = Habit::getCompletedHabits($date);

    return ApiResponseFormatter::formatResponse(
      200, 
      "success", 
      [
        "possibleHabits" => $possibleHabits,
        "completedHabits" => $completedHabits
      ]
    );

  }

  public static function getSummary() 
	{

    $sql = "SELECT
        D.id,
        D.date,
        (
          SELECT
            COUNT(*)
          FROM day_habits DH
          WHERE DH.day_id = D.id
        ) AS completed,
        (
          SELECT 
            COUNT(*)
          FROM habit_week_days HWD
          JOIN habits H ON H.id = HWD.habit_id
          WHERE
            WEEKDAY(D.date) = HWD.week_day
            AND H.created_at <= D.date
        ) AS amount
      FROM days D;
    ";

    try {
      
      $db = new Database();

      $results = $db->select($sql);

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

  public static function getPossibleHabits($date) 
	{

    $weekDay = date('w', strtotime($date)) + 1;
    
    $possibleHabitsQuery = "
      SELECT *
      FROM habits
      WHERE created_at <= :date
        AND id IN (
          SELECT habit_id
          FROM habit_week_days
          WHERE week_day = :weekDay
        )
    ";

    try {
      
      $db = new Database();

      $results = $db->select($possibleHabitsQuery, array(
        ":date"=>$date,
        ":weekDay"=>$weekDay
      ));

      return $results;

    } catch (\PDOException $e) {

      return array(
        "message" => $e->getMessage()
      );

    }

  }

  public static function getCompletedHabits($date) 
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
    ";

    try {
      
      $db = new Database();

      $results = $db->select($completedHabitsQuery, array(
        ":date"=>$formattedDate
      ));

      return $results;

    } catch (\PDOException $e) {

      return array(
        "message" => $e->getMessage()
      );

    }

  }

  public static function save($title, $weekDays) 
  {

    try {
      
      $db = new Database();

      $db->query("CALL sp_habits_create(:title, :weekDays)", array(
        ":title"=>$title,
        ":weekDays"=>$weekDays
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

  public static function toggle($idhabit) 
  {

    try {
      
      $db = new Database();

      $db->query("CALL sp_habits_toggle(:idhabit)", array(
        ":idhabit"=>$idhabit
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