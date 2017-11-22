<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 19.10.17
 * Time: 11:56
 */

namespace App\Model;

use Nette;


class FeedModel {
    protected $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function searchFeed(array $values){

        $animals =  $this->database->table('zvire')->where(array_filter($values));
        $ret_array = array();
        $i = 0;
        $k = 0;
        foreach ($animals as $animal) {
            $ret_array[$i] = array();
            foreach ($animal->related('krmeni') as $krmeni) {
                $ret_array[$i][$k] = array();
                $ret_array[$i][$k]['id_krmeni'] = array();
                $ret_array[$i][$k]['id_krmeni'] = $krmeni->id_krmeni;
                $ret_array[$i][$k]['jmeno'] = array();
                $ret_array[$i][$k]['jmeno'] = $animal->jmeno;
                $ret_array[$i][$k]['jeKrmeno'] = array();
                $ret_array[$i][$k]['jeKrmeno'] = $krmeni->jeKrmeno;
                $ret_array[$i][$k]['cas'] = array();
                $ret_array[$i][$k]['cas'] = substr($krmeni->cas,0,10);
                $ret_array[$i][$k]['druh'] = array();
                $ret_array[$i][$k]['druh'] = $krmeni->druh;
                $ret_array[$i][$k]['mnozstvi'] = array();
                $ret_array[$i][$k]['mnozstvi'] = $krmeni->mnozstvi;
                //echo $animal->jmeno." ";
                //echo $krmeni->id_krmeni ."</br>";
                $k++;
            }
            $k = 0;
            $i++;
        }

        asort($ret_array);

        return $ret_array;
    }

    public function allFeed(){

        return $this->database->table('krmeni');
    }

    public function addFeed(array $values)
    {
        $this->database->table('krmeni')->insert($values);
    }



}