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

    public function getAllTrainingsSelect(){
        $skoleni = $this->database->table('skoleni');
        $ret_array = array();
        foreach ($skoleni as $item){

            $ret_array[$item->id_skoleni] = array();
            $ret_array[$item->id_skoleni] = $item->nazev . ', ' . substr($item->datum,0,10);

        }
        asort($ret_array);
        return $ret_array;
    }

    public function getAllTrainingsSelectByRodneCislo($rd){
        $ma_skoleni = $this->database->table('ma_skoleni')->where('rd_osetrovatel', $rd );
        $ret_array = array();
        foreach ($ma_skoleni as $item){
            $itemSkoleni = $this->database->table('skoleni')->get($item->id_skoleni);
            $ret_array[$item->id] = array();
            $ret_array[$item->id] = ($itemSkoleni)->nazev. ', ' . substr(($itemSkoleni)->datum,0,10);

        }
        asort($ret_array);
        return $ret_array;
    }


    public function getTrainingByAnimalKind($id_druh_zvirete){
        return $this->database->table('druh_zvirete')->get($id_druh_zvirete)['naSkoleni'];
    }

    public function getTrainings()
    {
        $skoleni = $this->database->table('skoleni');
        $ret_array = array();
        foreach ($skoleni as $item){
            $ret_array[$item->id_skoleni] = array();
            $ret_array[$item->id_skoleni] = $item->nazev;
        }

        return $ret_array;
    }

    public function addTraining(array $values){
        $this->database->table('skoleni')->insert($values);
    }

    public function deleteTraining($id_skoleni){
        $this->database->table('skoleni')->where('id_skoleni', $id_skoleni)->delete();
    }

    public function removeTrainingToKeeper($values){
        $this->database->table('ma_skoleni')->where($values)->delete();
    }

    public function getTraining($id_skoleni){
        return $this->database->table('skoleni')->get($id_skoleni);
    }

    public function updateTraining(array $values){
        $this->database->table('skoleni')->where('id_skoleni', $values['id_skoleni'])->update($values);
    }

    public function isValidID($id_skoleni){
        $isItFalse = $this->database->table('skoleni')->get($id_skoleni);
        if (!$isItFalse)
            throw new Nette\Application\BadRequestException("Bad Request", 404);
    }

    public function addTrainingToKeeper(array  $values)
    {
        if ($this->database->table('ma_skoleni')->where($values)->count() == 0) {
            $this->database->table('ma_skoleni')->insert($values);
        }
    }

    public function getTrainingsByRodneCislo($rd){
        $osetrovatele = $this->database->table('osetrovatel')->get($rd);

        $skoleni = "";
        $i = 1;
        foreach ($osetrovatele->related('ma_skoleni') as $maSkoleni){
            if($skoleni != ""){
                $skoleni .= "\n";
            }
            $item_skoleni = $this->database->table('skoleni')->get(($maSkoleni)->id_skoleni);
            $skoleni .= "[".substr(($item_skoleni)->datum,0,10).'] '.($item_skoleni)->nazev;
            $i++;
        }

        return $skoleni;
    }

}