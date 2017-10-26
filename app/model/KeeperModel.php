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

    public function addKeeper(Nette\Utils\ArrayHash $values)
    {
        $this->database->table('osetrovatel')->insert(['jmeno' =>$values->jmeno,
            'prijmeni' =>$values->prijmeni,
            'login' =>$values->login,
            'rodne_cislo' =>$values->rodne_cislo,
            'datum_narozeni' =>$values->datum_narozeni,
            'titul' =>$values->titul,
            'adresa' =>$values->adresa,
            'tel_cislo' =>$values->tel_cislo,
            'pohlavi' =>$values->pohlavi,
            'role' => $values->role]);
    }


    public function addKeeperEmployee(Nette\Utils\ArrayHash $values)
    {
        $this->addKeeper($values);

        $this->database->table('zamestnanec')->insert(['mzda' =>$values->mzda,
                                                             'pozice' =>$values->pozice,
                                                             'specializace' =>$values->specializace,
                                                             'osetrovatel' =>$values->rodne_cislo]);
    }

    public function addKeeperVolunteer(Nette\Utils\ArrayHash $values)
    {
        $this->addKeeper($values);

        $this->database->table('dobrovolnik')->insert(['organizace' =>$values->organizace,
                                                             'zodpovedna_osoba' =>$values->rodne_cislo,
                                                             'osetrovatel' =>$values->rodne_cislo]);
    }

}