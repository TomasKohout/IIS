<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 20.11.17
 * Time: 11:08
 */

namespace App\Model;
use Nette;


class CoopModel
{
    protected $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function addCoop($values){
        $this->database->table('vybeh')->insert($values);
    }

    public function updateCoop($values){
        $this->database->table('vybeh')->where('id_vybeh', $values['id_vybeh'])->update($values);
    }

    public function showCoop(){
        return $this->database->table('vybeh')->fetchAll();
    }

    public function getTypeOfCoop(){
        $typ = $this->database->table('typ_vybehu');

        $ret_array = array();

        foreach ($typ as $row){
            $ret_array[$row->id_typ_vybehu] = array();
            $ret_array[$row->id_typ_vybehu] = $row->velikost;
        }

        return $ret_array;
    }

    public function getCoops(){
        $coops = $this->database->table('vybeh')->select('id_vybeh');

        $ret_array = array();

        foreach ($coops as $row){
            $ret_array[$row->id_vybeh] = array();
            $ret_array[$row->id_vybeh] = $row->id_vybeh;
        }

        return $ret_array;
    }

    public function isValidID($id_vybeh){
        $testIfIsFalse = $this->database->table('vybeh')->get($id_vybeh);
        if (!$testIfIsFalse)
            throw new Nette\Application\BadRequestException("Bad Request", "404");
    }

    public function getCoopValues($id_vybeh){
        return $this->database->table('vybeh')->get($id_vybeh);
    }

    public function searchCoop($values)
    {
        return $this->database->table('vybeh')->where(array_filter($values));
    }

}