<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 19.10.17
 * Time: 11:56
 */

namespace App\Model;

use Nette;

class AnimalModel {
    protected $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }


    /**
     * @param $id_zvire
     * @return Nette\Database\Table\ActiveRow
     */
    public function getAnimalValues($id_zvire){
        return $this->database->table('zvire')->get($id_zvire);
    }

    public function addAnimal(array $values)
    {
        $this->database->table('zvire')->insert($values);
    }

    public function updateAnimal(array $values){
        $this->database->table('zvire')->where('id_zvire', $values['id_zvire'])
            ->update($values);
    }

    public function searchAnimal(array $values){
        return $this->database->table('zvire')->where(array_filter($values));
    }

    public function allAnimals(){

        return $this->database->table('zvire');
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

    public function getDefaultValuesForZvireTable(){

    }

    public function addDruh(array $values){
        $this->database->table('druh_zvirete')->insert($values);
    }

}