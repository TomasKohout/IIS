<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 19.10.17
 * Time: 11:56
 */

namespace App\Model;

use Nette;
use function Sodium\library_version_minor;


class FeedModel {

    protected $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function searchByDatum($date){
        return $this->database->table('krmeni')->where('datum' , $date)->count('id_krmeni');
    }

    public function searchByLogin($login){
        $row = $this->database->table('osetrovatel')->where('login', $login);
        return $this->database->table('provadi_krmeni')->where('rd_osetrovatel')->count('rd_osetrovatel');
    }

    public function searchFeed(array $values = []){

        $date = "";
        $searchingLogin = "";
        if(isset($values['datum'])) {
            $date = $values['datum'];
            unset($values['datum']);
        }
        if(isset($values['login']))
        {
            $searchingLogin = $values['login'];
            unset($values['login']);
        }
        $animals =  $this->database->table('zvire')->where(array_filter($values));




        $ret_array = array();
        $k = 0;
        foreach ($animals as $animal) {
            foreach ($animal->related('krmeni') as $krmeni) {
                if ($date != "" && $date != substr($krmeni->datum, 0, 10)) {
                    continue;
                }

                $login = "";
                $tmp = "";
                foreach($krmeni->related('provadi_krmeni') as $provadi){
                    $tmp = $this->database->table('osetrovatel')->get($provadi->rd_osetrovatel);
                    if($login != ""){
                        $login .= ', ';
                    }
                    $login = $login.$tmp->login;
                }
                if ($searchingLogin != "" && !(strpos( $login, $searchingLogin) !== false)) {
                    continue;
                }
                $ret_array[$k] = array();
                $ret_array[$k]['id_krmeni'] = array();
                $ret_array[$k]['id_krmeni'] = $krmeni->id_krmeni;
                $ret_array[$k]['login'] = array();
                $ret_array[$k]['login'] = $login;
                $ret_array[$k]['jmeno'] = array();
                $ret_array[$k]['jmeno'] = $animal->jmeno;
                $ret_array[$k]['jeKrmeno'] = array();
                $ret_array[$k]['jeKrmeno'] = $krmeni->jeKrmeno;
                $ret_array[$k]['datum'] = array();
                $ret_array[$k]['datum'] = substr($krmeni->datum, 0, 10);
                $ret_array[$k]['druh'] = array();
                $ret_array[$k]['druh'] = $krmeni->druh;
                $ret_array[$k]['mnozstvi'] = array();
                $ret_array[$k]['mnozstvi'] = $krmeni->mnozstvi;
                //echo $animal->jmeno." ";
                //echo $krmeni->id_krmeni ."</br>";
                $k++;
            }
        }


        arsort($ret_array);
        return $ret_array;
    }

    public function addFeed(array $values)
    {
        $valuesKrmeni = $values;
        unset($valuesKrmeni['rd_osetrovatel']);
        $valuesProvadiKrmeni = $values;
        unset($valuesProvadiKrmeni['jeKrmeno']);
        unset($valuesProvadiKrmeni['datum']);
        unset($valuesProvadiKrmeni['druh']);
        unset($valuesProvadiKrmeni['mnozstvi']);

        $row = $this->database->table('krmeni')->insert($valuesKrmeni);
        $last = $row->getPrimary();
        $valuesProvadiKrmeni['id_krmeni'] = $last;

        $this->database->table('provadi_krmeni')->insert($valuesProvadiKrmeni);
    }

    public function getRodneCisloByLoginWithTraining($id_zvire)
    {
        $osetrovatele = $this->database->table('osetrovatel');


        $zvire = $this->database->table('zvire')->get($id_zvire);
        $druh_zvirete = $this->database->table('druh_zvirete')->get($zvire->jeDruhu);
        $skoleni = $this->database->table('skoleni')->get($druh_zvirete->naSkoleni)->id_skoleni;


        $ret_array = array();
        foreach($osetrovatele as $osetrovatel) {
            foreach($osetrovatel->related('ma_skoleni', 'rd_osetrovatel') as $maSkoleni) {
                if (!strcmp($maSkoleni->skoleni->id_skoleni, $skoleni)) {
                    if (strcmp($osetrovatel->login, "admin") == 0)
                        continue;

                    $ret_array[$osetrovatel->rodne_cislo] = array();
                    $ret_array[$osetrovatel->rodne_cislo] = $osetrovatel->login;
                }

            }
        }
        return $ret_array;
    }

    public function getCountOfFeeds($array = [])
    {
        return $this->database->table('krmeni')->where(array_filter($array))->count('id_krmeni');
    }


}