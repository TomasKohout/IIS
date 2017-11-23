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

}