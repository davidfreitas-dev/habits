<?php

namespace App\Models;

use App\DB\Database;
use App\Models\Model;
use App\Enums\HttpStatus as HTTPStatus;

class Habit extends Model {
  
  public function __construct(Database $db)
  {
    
    $this->db = $db; // Já existe na classe Model
    
    parent::__construct($db);
    
  }

  public function create() 
  {

    $this->checkHabitExists($this->getTitle());
    
    $results = $this->db->select(
      "CALL sp_habits_create(:title, :week_days, :user_id)", 
      [
        ":title"     => $this->getTitle(),
        ":week_days" => $this->getWeekDays(),
        ":user_id"   => $this->getUserId()
      ]
    );

    return $results[0];

  }

  public function update() 
  {

    $this->checkHabitExists($this->getTitle(), $this->getId());

    $results = $this->db->select(
      "CALL sp_habits_update(:id, :title, :week_days)", 
      [
        ":id"        => $this->getId(),
        ":title"     => $this->getTitle(),
        ":week_days" => $this->getWeekDays()
      ]
    );

    return $results[0];

  }

  public function get($id)
  {

    $sql = "SELECT 
              h.id,
              h.title,
              GROUP_CONCAT(hwd.week_day ORDER BY hwd.week_day) AS week_days
            FROM 
              habits h
            LEFT JOIN 
              habit_week_days hwd ON h.id = hwd.habit_id
            WHERE 
              h.id = :id
            GROUP BY 
              h.id, h.title";

    $results = $this->db->select($sql, array(
      ":id" => $id
    ));

    if (empty($results)) {
      
      throw new \Exception("Hábito não encontrado.", HTTPStatus::NOT_FOUND);

    }

    return $results[0];
    
  }

  public function list($userId, $date) 
  {

    $possibleHabits = $this->getPossibleHabits($date, $userId);

    $completedHabits = $this->getCompletedHabits($date, $userId);

    return [
      "possibleHabits"  => $possibleHabits,
      "completedHabits" => $completedHabits
    ];

  }

  public function summary($userId) 
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
            FROM days D";

    $results = $this->db->select($sql, array(
      ":userId" => $userId
    ));

    if (empty($results)) {

      throw new \Exception("Resumo não disponível para o dia selecionado.", HTTPStatus::NOT_FOUND);        		

    } 

    return $results; 

  }

  private function getPossibleHabits($date, $userId) 
	{

    $weekDay = date('w', strtotime($date));
    
    $possibleHabitsQuery = "SELECT 
                                h.*,
                                (SELECT 
                                    GROUP_CONCAT(hwd.week_day ORDER BY hwd.week_day)
                                FROM 
                                    habit_week_days hwd
                                WHERE 
                                    hwd.habit_id = h.id) AS week_days
                            FROM 
                                habits h
                            WHERE 
                                h.created_at <= :date
                                AND h.id IN (
                                    SELECT 
                                        habit_id
                                    FROM 
                                        habit_week_days
                                    WHERE 
                                        week_day = :weekDay
                                )
                                AND h.user_id = :userId";

    $results = $this->db->select($possibleHabitsQuery, array(
      ":date"=>$date,
      ":weekDay"=>$weekDay,
      ":userId"=>$userId
    ));

    return $results;

  }

  private function getCompletedHabits($date, $userId) 
	{

    $formattedDate = date('Y-m-d', strtotime($date));

    $completedHabitsQuery = "SELECT 
                                dh.habit_id,
                                (SELECT 
                                    GROUP_CONCAT(hwd.week_day ORDER BY hwd.week_day)
                                FROM 
                                    habit_week_days hwd
                                WHERE 
                                    hwd.habit_id = dh.habit_id) AS week_days
                            FROM 
                                day_habits dh
                            WHERE 
                                dh.day_id = (
                                    SELECT 
                                        id
                                    FROM 
                                        days
                                    WHERE 
                                        date = :date
                                )
                                AND dh.habit_id IN (
                                    SELECT 
                                        id
                                    FROM 
                                        habits
                                    WHERE 
                                        user_id = :userId
                                )";

    $db = new Database();

    $results = $this->db->select($completedHabitsQuery, array(
      ":date"=>$formattedDate,
      ":userId"=>$userId
    ));

    return $results;

  }

  public function toggle($userId, $habitId) 
  {

    $currentDate = date('Y-m-d H:i:s', strtotime('today'));

    $affectedRows = $this->db->query("CALL sp_habits_toggle(:userId, :habitId, :currentDate)", array(
      ":userId"=>$userId,
      ":habitId"=>$habitId,
      ":currentDate"=>$currentDate
    ));

    if ($affectedRows === 0) {
        
      throw new \Exception("Usuário não encontrado.", HTTPStatus::NOT_FOUND);
      
    }

  }

  public function delete($id) 
	{
		
		$sql = "DELETE FROM habits WHERE id = :id";
		
		$affectedRows = $this->db->query($sql, array(
      ':id' => $id
    ));

    if ($affectedRows === 0) {
        
      throw new \Exception("Usuário não encontrado.", HTTPStatus::NOT_FOUND);
      
    }	

	}

  private function checkHabitExists($title, $id = NULL) 
  {

    $sql = "SELECT * FROM habits WHERE title = :title";

    if ($id) {

      $sql .= " AND id != :id";

    }

    $params = [
      ":title" => $title
    ];

    if ($id) {

      $params[":id"] = $id;

    }
      
    $results = $this->db->select($sql, $params);

    if (count($results) > 0) {
      
      throw new \Exception("Já existe um hábito com este título.", HTTPStatus::BAD_REQUEST);

    }

  }

}