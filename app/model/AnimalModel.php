<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 19.10.17
 * Time: 11:56
 */

namespace App\Model;

use Nette\Application\BadRequestException;
use Nette;

class AnimalModel {
    protected $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }


    public function getAnimalValues($id_zvire){
        return $this->database->table('zvire')->get($id_zvire);
    }

    public function getAnimalKindValues($id_druh_zvirete){
        return $this->database->table('druh_zvirete')->get($id_druh_zvirete);
    }

    public function isValidId($id_zvire){
        $testIfIsFalse = $this->database->table('zvire')->get($id_zvire);
        if (!$testIfIsFalse)
            throw new BadRequestException("Bad Request", 404);
    }

    public function addAnimal(array $values)
    {
        $this->database->table('zvire')->insert($values);
    }

    public function updateAnimal(array $values){
        $this->database->table('zvire')->where('id_zvire', $values['id_zvire'])
            ->update($values);
    }

    public function updateAnimalKind($values){
        $this->database->table('druh_zvirete')->where('id_druh_zvirete', $values['id_druh_zvirete'])
            ->update($values);
    }


    public function deleteAnimalKind($id_druh_zvirete){
        $this->database->table('druh_zvirete')->get($id_druh_zvirete)->delete();
    }

    public function killAnimal(array $values){
        $this->database->table('zvire')->where('id_zvire', $values['id_zvire'])
            ->update([  'datum_umrti' =>$values['datum_umrti'],
                        'obyva' => null]);
    }

    public function searchAnimal(array $values){
        return $this->database->table('zvire')->where(array_filter($values));
    }

    public function searchKind($getValues)
    {
        return $this->database->table('druh_zvirete')->where(array_filter($getValues));

    }

    /**
     * @param $id_zvire
     * @return bool
     */
    public function isDead($id_zvire){
        $tmp = $this->database->table('zvire')->get($id_zvire);
        if (!$tmp)
            throw new BadRequestException("", 404);
        return "" != $tmp->datum_umrti;

    }

    public function allAnimals($limit, $offset,array $array = null){

        if (empty($array))
            return $this->database->table('zvire')->order('id_zvire ASC')->limit($limit, $offset);
        else
            return $this->database->table('zvire')->where(array_filter($array))->order('id_zvire ASC')->limit($limit, $offset);
    }

    public function getCountOfAnimals($array = null)
    {
        if (empty($array))
            return $this->database->table('zvire')->count('id_zvire');
        else
            return $this->database->table('zvire')->where(array_filter($array))->count('id_zvire');
    }

    public function getTypVybehu()
    {
        $vybeh = $this->database->table('vybeh');

        $ret_array = array();
        $poloha = "Pavilon-A";
        $ret_array[$poloha] = array();
        foreach ($vybeh as $one_vybeh)
        {
            if (strcmp($one_vybeh->poloha, $poloha) != 0)
            {
                $poloha = $one_vybeh->poloha;
                if (!isset($ret_array[$poloha])){
                    $ret_array[$poloha] = array();
                }
            }
            $ret_array[$poloha][$one_vybeh->id_vybeh] = $one_vybeh->id_vybeh;
        }

        return $ret_array;
    }

    public function getZvire()
    {
        $zvire = $this->database->table('zvire');

        $ret_array = array();

        foreach ($zvire as $one_zvire){

            $ret_array[$one_zvire->id_zvire] = array();
            $ret_array[$one_zvire->id_zvire] = $one_zvire->jmeno;
        }

        return $ret_array;
    }

    public function getDruh(){
        $druh_zvirete = $this->database->table('druh_zvirete');

        $ret_array = array();

        foreach ($druh_zvirete as $druh)
        {
            $ret_array[$druh->id_druh_zvirete] = array();
            $ret_array[$druh->id_druh_zvirete] = $druh->nazev;
        }
        return $ret_array;

    }

    public function kindIsNotExist($id_druh_zvirete){
        $tmp = $this->database->table('druh_zvirete')->get($id_druh_zvirete);
        if (!$tmp) {
            throw new BadRequestException("", 404);
        }
        return true;
    }

    public function addDruh(array $values){
        $this->database->table('druh_zvirete')->insert($values);
    }

}