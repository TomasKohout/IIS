<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 23.10.17
 * Time: 12:16
 */

namespace App\Presenters;

use App\Model\KeeperModel;
use Nette\Application\UI\Form;
use Nette;

class AddKeeperPresenter extends BasePresenter
{
    protected $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function renderDefault()
    {

    }

    public function createComponentAddKeeper()
    {
        $model = new KeeperModel($this->database);
        $form = $this->form();
        $form->addText('jmeno', 'Jméno: ')
            ->setRequired();
        $form->addText('prijmeni', 'Příjmení: ')
            ->setRequired();
        $form->addText('rodne_cislo', 'Rodné číslo: ')
            ->setRequired();
        $sex = ['M' => 'muž', 'Z' => 'žena'];
        $form->addRadioList('pohlavi', 'Pohlaví:', $sex)
            ->setRequired();
        $form->addText('datum_narozeni', "Datum narození:")
            ->setRequired("Datum narození je povinný údaj")
            ->setAttribute("class", "dtpicker col-sm-2")
            ->setAttribute('placeholder', 'rrrr.mm.dd')
            ->addRule($form::PATTERN, "Datum musí být ve formátu YYYY.MM.DD", "(19|20)\d\d\.(0[1-9]|1[012])\.(0[1-9]|[12][0-9]|r[01])");
        $form->addText('tel_cislo', 'Telefoní číslo: ')
            ->setRequired();
        $form->addText('adresa', 'Bydliště: ')
            ->setRequired();
        $form->addText('titul', 'Tituly: ');
        $form->addText('login', 'Uživatelské jméno:')
            ->setRequired();
        $form->addText('heslo', 'Heslo:')
            ->setRequired();


        $role = ['0' => 'Admin', '1' => 'Zaměstnanec', '2' => 'Dobrovolník'];
        $form->addRadioList('role', 'Typ zařadění: ', $role)->setDefaultValue('1')
            ->addCondition($form::EQUAL, '1')
                ->toggle('mzda')
                ->toggle('specializace')
                ->toggle('pozice')
            ->endCondition()
            ->addCondition($form::EQUAL, '2')
                ->toggle('organizace')
                ->toggle('zodpovedna_osoba');

        //Zaměstanec
        $form->addText('mzda', 'Mzda: ')
            ->setOption('id', 'mzda');
        $form->addText('specializace', 'Specializace: ')
            ->setOption('id', 'specializace');
        $form->addText('pozice', 'Pozice: ')
            ->setOption('id', 'pozice');


        //Dobrovolnik
        $form->addText('organizace', 'Organizace: ')
            ->setOption('id', 'organizace');
        $form->addSelect('zodpovedna_osoba', 'Zodpovědná osoba: ', $model->getRodneCisloByLogin())
            ->setOption('id', 'zodpovedna_osoba');



        $form->addSubmit('submit', 'Přidat');
        $form->onSuccess[] = [$this, 'addKeeperSucceed'];
        return $form;

    }

    public function addKeeperSucceed(Form $form, Nette\Utils\ArrayHash $values)
    {
        $model = new KeeperModel($this->database);
        if($values->role == 0){
            $model->addKeeper($values);
        } else if($values->role == 1) {
            $model->addKeeperEmployee($values);
        } else {
            $model->addKeeperVolunteer($values);
        }

        $this->flashMessage('Záznam přidán!' ,'success');
        $this->redirect('AddKeeper:');

    }

}