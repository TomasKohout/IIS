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
        $date = "";
        $searchingLogin = "";
        if(isset($values['datum'])) {
            $date = $values['datum'];
            unset($values['datum']);
            $searchingLogin = $values['login'];
            unset($values['login']);
        }
        $animals =  $this->database->table('zvire')->where(array_filter($values));



        $ret_array = array();
        $i = 0;
        $k = 0;
        foreach ($animals as $animal) {
            $ret_array[$i] = array();
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
                    $tmp = $tmp->login;
                }
                if ($searchingLogin != "" && !(strpos( $tmp, $searchingLogin) !== false)) {
                    continue;
                }
                $ret_array[$i][$k] = array();
                $ret_array[$i][$k]['login'] = array();
                $ret_array[$i][$k]['login'] = $login;
                $ret_array[$i][$k]['id_krmeni'] = array();
                $ret_array[$i][$k]['id_krmeni'] = $krmeni->id_krmeni;
                $ret_array[$i][$k]['jmeno'] = array();
                $ret_array[$i][$k]['jmeno'] = $animal->jmeno;
                $ret_array[$i][$k]['jeKrmeno'] = array();
                $ret_array[$i][$k]['jeKrmeno'] = $krmeni->jeKrmeno;
                $ret_array[$i][$k]['datum'] = array();
                $ret_array[$i][$k]['datum'] = substr($krmeni->datum, 0, 10);
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

        $i = 0;
        $sorted_ret = array();
        foreach ($ret_array as $zvire){
            foreach ($zvire as $krmeni){
                $sorted_ret[$i] = array();
                $sorted_ret[$i]['id_krmeni'] = $krmeni['id_krmeni'];
                $sorted_ret[$i]['jeKrmeno'] = $krmeni['jeKrmeno'];
                $sorted_ret[$i]['jmeno'] = $krmeni['jmeno'];
                $sorted_ret[$i]['login'] = $krmeni['login'];
                $sorted_ret[$i]['druh'] = $krmeni['druh'];
                $sorted_ret[$i]['mnozstvi'] = $krmeni['mnozstvi'];
                $sorted_ret[$i]['datum'] = $krmeni['datum'];
                $i++;
            }
        }
        arsort($sorted_ret);

        return $sorted_ret;
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


}