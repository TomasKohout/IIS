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

    public function addCoopKind($values){
        $this->database->table('typ_vybehu')->insert($values);
    }

    public function deleteCoopKind($id_typ_vybehu){
        $this->database->table('typ_vybehu')->get($id_typ_vybehu)->delete();
    }


    public function updateCoopKind($values){
        $this->database->table('typ_vybehu')->where('id_typ_vybehu', $values['id_typ_vybehu'])->update($values);
    }

    public function updateCoop($values){
        $this->database->table('vybeh')->where(array_filter($values))->update($values);
    }

    public function showCoop(){
        return $this->database->table('vybeh')->fetchAll();
    }

    public function searchCoopKind($values){
        return $this->database->table('typ_vybehu')->where(array_filter($values));
    }

    public function getCoopKindValues($id_typ_vybehu){
        return $this->database->table('typ_vybehu')->get($id_typ_vybehu);
    }

    public function getTypeOfCoop(){
        $typ = $this->database->table('typ_vybehu');

        $ret_array = array();

        foreach ($typ as $row){
            $ret_array[$row->id_typ_vybehu] = array();
            $ret_array[$row->id_typ_vybehu] = $row->nazev;
        }

        return $ret_array;
    }


    public function kindIsNotExist($id_typ_vybehu){
        $tmp = $this->database->table('typ_vybehu')->get($id_typ_vybehu);
        if (!$tmp) {
            throw new BadRequestException("", 404);
        }
        return true;

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