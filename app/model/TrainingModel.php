<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 20.11.17
 * Time: 17:39
 */

namespace App\Model;
use Nette;

class TrainingModel
{
    protected $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function getAllTrainings(){
        return $this->database->table('skoleni')->fetchAll();
    }

    public function searchTrainings($values){
        return $this->database->table('skoleni')->where(array_filter($values));
    }

    public function getTrainings()
    {
        $skoleni = $this->database->table('skoleni');
        $ret_array = array();
        foreach ($skoleni as $item){
            if (strlen($item->nazev) != 1)
            {
                $ret_array[$item->id_skoleni] = array();
                $ret_array[$item->id_skoleni] = $item->nazev;
            }
        }

        return $ret_array;
    }

    public function addTraining(array $values){
        $this->database->table('skoleni')->insert($values);
    }

    public function deleteTraining($id_skoleni){
        $this->database->table('skoleni')->where('id_skoleni', $id_skoleni)->delete();
    }

    public function getTraining($id_skoleni){
        return $this->database->table('skoleni')->get($id_skoleni);
    }

    public function updateTraining(array $values){
        $this->database->table('skoleni')->where('id_skoleni', $values['id_skoleni'])->update($values);
    }

}