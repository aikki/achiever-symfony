<?php
namespace App\Entity;

use Exception;

class ClubData
{
    private $club;
    private $goals;
    private $goalsAchieved;
    private $goalsAchievedNumber;
    private $goalsNotAchieved;

    public function __call($name, $params) {
        if ($this->club instanceof Club) {
            $method = 'get'.ucfirst($name);
            if (method_exists($this->club, $method)) {
                return $this->club->{$method}();
            }
        }
        throw new Exception('Method "Club::' . $method . '()" does not exists!');
    }

    public function getClub() {
        return $this->club;
    }
    public function setClub($club) {
        $this->club = $club;
    }
    public function getGoals() {
        return $this->goals;
    }
    public function setGoals($goals) {
        $this->goals = $goals;
    }
    public function getGoalsNumber() {
        return count($this->goals);
    }
    public function getGoalsAchieved() {
        return $this->goalsAchieved;
    }
    public function setGoalsAchieved($goalsAchieved) {
        $this->goalsAchieved = $goalsAchieved;
    }
    public function getGoalsAchievedNumber() {
        return $this->goalsAchievedNumber;
    }
    public function setGoalsAchievedNumber($goalsAchievedNumber) {
        $this->goalsAchievedNumber = $goalsAchievedNumber;
    }
    public function getGoalsNotAchieved() {
        return $this->goalsNotAchieved;
    }
    public function setGoalsNotAchieved($goalsNotAchieved) {
        $this->goalsNotAchieved = $goalsNotAchieved;
    }
}