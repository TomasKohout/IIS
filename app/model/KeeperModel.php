<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 26.10.17
 * Time: 13:55
 */

namespace App\Model;

use Nette;

class KeeperModel
{
    protected $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function checkForPass($id_keeper)
    {
        return "1234567" === $this->database->table('osetrovatel')->get($id_keeper)->heslo;
    }

    public function getKeeperValues($rodne_cislo){
        return $this->database->table('osetrovatel')->get($rodne_cislo);
    }

    public function getEmployeeKeeperValues($rodne_cislo){
        return $this->database->table('zamestnanec')->get($rodne_cislo);
    }

    public function getVolunteerKeeperValues($rodne_cislo){
        return $this->database->table('dobrovolnik')->get($rodne_cislo);
    }



    public function allKeeper(){

        return $this->database->table('osetrovatel');
    }

    public function searchKeeper($values){
        return $this->database->table('osetrovatel')->where(array_filter($values))->order('login');
    }

    public function updateKeeper( $values){
        $this->database->table('osetrovatel')->where('rodne_cislo', $values['rodne_cislo'])
            ->update(['jmeno' =>$values->jmeno,
                        'prijmeni' =>$values->prijmeni,
                        'login' =>$values->login,
                        'rodne_cislo' =>$values->rodne_cislo,
                        'datum_narozeni' =>$values->datum_narozeni,
                        'titul' =>$values->titul,
                        'adresa' =>$values->adresa,
                        'tel_cislo' =>$values->tel_cislo,
                        'pohlavi' =>$values->pohlavi]);
    }

    public function updateKeeperEmployee($values){
        $this->updateKeeper($values);
        $this->database->table('zamestnanec')->where('osetrovatel', $values['rodne_cislo'])
            ->update([  'mzda' =>$values->mzda,
                        'pozice' =>$values->pozice,
                        'specializace' =>$values->specializace,
                        'osetrovatel' =>$values->rodne_cislo]);
    }

    public function updateKeeperVolunteer($values){
        $this->updateKeeper($values);
        $this->database->table('dobrovolnik')->where('osetrovatel', $values['rodne_cislo'])
            ->update([  'organizace' =>$values->organizace,
                        'zodpovedna_osoba' =>$values->zodpovedna_osoba,
                        'osetrovatel' =>$values->rodne_cislo]);
    }


    public function addKeeper(Nette\Utils\ArrayHash $values)
    {
        $this->database->table('osetrovatel')
            ->insert(['jmeno' =>$values->jmeno,
                      'prijmeni' =>$values->prijmeni,
                      'login' =>$values->login,
                      'rodne_cislo' => str_replace("/", "", $values->rodne_cislo),
                      'datum_narozeni' =>$values->datum_narozeni,
                      'titul' =>$values->titul,
                      'adresa' =>$values->adresa,
                      'tel_cislo' =>$values->tel_cislo,
                      'pohlavi' =>$values->pohlavi,
                      'heslo' => "1234567",
                      'role' => $values->role]);
    }


    public function addKeeperEmployee(Nette\Utils\ArrayHash $values)
    {
        $this->addKeeper($values);

        $this->database->table('zamestnanec')
            ->insert(['mzda' =>$values->mzda,
                      'pozice' =>$values->pozice,
                      'specializace' =>$values->specializace,
                      'osetrovatel' =>$values->rodne_cislo]);
    }

    public function addKeeperVolunteer(Nette\Utils\ArrayHash $values)
    {
        $this->addKeeper($values);

        $this->database->table('dobrovolnik')
            ->insert(['organizace' =>$values->organizace,
                      'zodpovedna_osoba' =>$values->zodpovedna_osoba,
                      'osetrovatel' =>$values->rodne_cislo]);
    }


    public function getRodneCisloByLogin()
    {
        $osetrovatel = $this->database->table('osetrovatel');

        $ret_array = array();

        foreach ($osetrovatel as $one){

            if (strcmp($one->login, "admin") == 0)
                continue;

            $ret_array[$one->rodne_cislo] = array();
            $ret_array[$one->rodne_cislo] = $one->login;
        }

        return $ret_array;
    }

    public function getLogin($login){
        return $this->database->table('osetrovatel')->where('login', $login);
    }

    public function isValidRodneCislo($rodne_cislo){
        $testIfIsFalse = $this->database->table('osetrovatel')->get($rodne_cislo);
        if (!$testIfIsFalse)
            throw new Nette\Application\BadRequestException("Bad Request", 404);
    }


}

