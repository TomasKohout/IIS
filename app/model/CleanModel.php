<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 19.10.17
 * Time: 11:56
 */

namespace App\Model;

use Instante\ExtendedFormMacros\PairAttributes;
use Nette;


class CleanModel {
    protected $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

//    public function searchClean(array $values){
//        return $this->database->table('cisteni')->where(array_filter($values));
//    }

    public function searchClean( array $values =[]){
        $date = "";
        $searchingLogin = "";
        if(isset($values['datum'])) {
            $date = $values['datum'];
            unset($values['datum']);
        }
        if(isset($values['login'])){
            $searchingLogin = $values['login'];
            unset($values['login']);
        }
        $vybehy =  $this->database->table('vybeh')->where(array_filter($values));

        $ret_array = array();
        $k = 0;
        foreach ($vybehy as $vybeh) {
            foreach ($vybeh->related('cisteni') as $cisteni) {
                if ($date != "" && $date != substr($cisteni->datum, 0, 10)) {
                    continue;
                }
                $login = "";
                $tmp = "";
                foreach($cisteni->related('provadi_cisteni') as $provadi){
                    $osetrovatel = $provadi->rd_osetrovatel;
                    $tmp = $this->database->table('osetrovatel')->get($osetrovatel);
                    if($login != ""){
                        $login .= ', ';
                    }
                    $login = $login.$tmp->login;
                }
                if ($searchingLogin != "" && !(strpos( $login, $searchingLogin) !== false)) {
                    continue;
                }
                $ret_array[$k] = array();
                $ret_array[$k]['id_cisteni'] = array();
                $ret_array[$k]['id_cisteni'] = $cisteni->id_cisteni;
                $ret_array[$k]['jeCisten'] = array();
                $ret_array[$k]['jeCisten'] = $cisteni->jeCisten;
                $ret_array[$k]['login'] = array();
                $ret_array[$k]['login'] = $login;
                $ret_array[$k]['datum'] = array();
                $ret_array[$k]['datum'] = substr($cisteni->datum, 0, 10);
                //echo $vybeh->jmeno." ";
                //echo $cisteni->id_krmeni ."</br>";
                $k++;
            }

        }
        arsort($ret_array);
        return $ret_array;
    }


    public function addClean(array $values){
        $valuesCisteni = $values;
        unset($valuesCisteni['osetrovatele']);

        $row = $this->database->table('cisteni')->insert($valuesCisteni);
        $last = $row->getPrimary();

        foreach ($values['osetrovatele'] as $osetrovatel){
            $this->database->table('provadi_cisteni')->insert([
                'id_cisteni' => $last,
                'rd_osetrovatel' => $osetrovatel['rd_osetrovatel']]);
        }
    }


    public function getCleaners($id_cisteni){

        $cisteni = $this->database->table('provadi_cisteni')->where('id_cisteni', $id_cisteni);

        $cleaners = '';
        foreach ($cisteni as $cist){
            $tmp = $this->database->table('osetrovatel')->get($cist->rd_osetrovatel);
            $cleaners .= ' '.$tmp->login;
        }
        return $cleaners;
    }

    public  function  getNumberOfNeededKeepersToClean($id_vybeh){
        $vybeh = $this->database->table('vybeh')->get($id_vybeh);

        $typ_vybehu = $this->database->table('typ_vybehu')->get($vybeh->naTypVybehu);

        return $typ_vybehu->pocet_osetrovatelu;
    }


    public function getRodneCisloByLoginWithTraining($id_vybeh)
    {
        $osetrovatele = $this->database->table('osetrovatel');


        $vybeh = $this->database->table('vybeh')->get($id_vybeh);
        $typ_vybehu = $this->database->table('typ_vybehu')->get($vybeh->naTypVybehu);
        $skoleni = $this->database->table('skoleni')->get($typ_vybehu->naSkoleni)->id_skoleni;


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

    public function getCountOfCisteniByDatum($datum)
    {
        return $this->database->table('cisteni')->where('datum', $datum)->count('id_cisteni');
    }

    public function getCountOfCisteniByLogin($login)
    {
        $selection = $this->database->table('osetrovatel')->where('login', $login);
        $row = $selection->fetch();
        return $this->database->table('provadi_cisteni')->where([])->count('rd_osetrovatel');
    }

}