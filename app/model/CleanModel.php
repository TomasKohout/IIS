<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 19.10.17
 * Time: 11:56
 */

namespace App\Model;

use Nette;


class CleanModel {
    protected $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function searchClean(array $values){
        return $this->database->table('cisteni')->where(array_filter($values));
    }

    public function allClean(){

        return $this->database->table('cisteni');
    }

    public function addClean(array $values)
    {
        $valuesCisteni = $values;
        unset($valuesCisteni['rd_osetrovatel']);

        $valuesProvadiCisteni = $values;
        unset($valuesProvadiCisteni['jeCisten']);
        unset($valuesProvadiCisteni['datum']);

        $row = $this->database->table('cisteni')->insert($valuesCisteni);
        $last = $row->getPrimary();
        $valuesProvadiCisteni['id_cisteni'] = $last;
        $this->database->table('provadi_cisteni')->insert($valuesProvadiCisteni);
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


    public  function  getNumberOfNeededKeepersToClean($id_vybeh){
        $vybeh = $this->database->table('vybeh')->get($id_vybeh);

        $pocet = array();
        $pocet['id_vybeh'] = "";
        $pocet['pocet_osetrovatelu'] = "";
        foreach($vybeh->related('typ_vybehu', 'id_typ_vybehu') as $typ){
            $pocet['id_vybeh'] = ($vybeh)->id_vybeh;
            $pocet['pocet_osetrovatelu'] = ($typ)->pocet_osetrovatelu;
        }
        return $pocet;
    }


    public function getRodneCisloByLoginWithTraining($id_vybeh)
    {
        $osetrovatele = $this->database->table('osetrovatel');


        $vybeh = $this->database->table('vybeh')->get($id_vybeh);

        $vybehAndSkoleni = array();
        $vybehAndSkoleni['id_vybeh'] = "";
        $vybehAndSkoleni['id_skoleni'] = "";
        foreach($vybeh->related('typ_vybehu', 'id_typ_vybehu') as $typ){
            $vybehAndSkoleni['id_vybeh'] = $vybeh->id_vybeh;
            $vybehAndSkoleni['id_skoleni'] = $typ->skoleni->id_skoleni;
        }


        $ret_array = array();
        foreach($osetrovatele as $osetrovatel) {
            foreach($osetrovatel->related('ma_skoleni', 'rd_osetrovatel') as $maSkoleni) {
                if (!strcmp($maSkoleni->skoleni->id_skoleni, $vybehAndSkoleni['id_skoleni'])) {
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