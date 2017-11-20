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

    public function getAllTraining(){
        return $this->database->table('skoleni')->fetchAll();
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

    public function addSkoleni(array $values){
        $this->database->table('skoleni')->insert($values);
    }


}