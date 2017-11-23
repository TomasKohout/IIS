<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 23.10.17
 * Time: 12:16
 */

namespace App\Presenters;

use App\Forms\MyValidation;
use App\Model\KeeperModel;
use App\Model\RodneCisloException;
use Nette\Application\UI\Form;
use Nette;

class KeeperPresenter extends BasePresenter
{
    protected $database;
    protected $model;
    protected $rodne_cislo;

    public function __construct(Nette\Database\Context $database)
    {

        $this->database = $database;
        $this->model =  new KeeperModel($database);
    }

    protected function startup(){
        parent::startup();
        if (!$this->user->isAllowed('admin'))
        {
            $this->flashMessage('Pro přístup na tuto stránku nemáte oprávnění. Obraťte se prosím na administrátora.', 'warning');
            $this->redirect('MainPage:default');
        }
    }

    public function renderAdd(){

    }

    public function renderSearch(){
        $this->template->dataAll = $this->model->allKeeper();

    }

    public function renderUpdate($rodne_cislo){
        $this->model->isValidRodneCislo($rodne_cislo);
        $this->rodne_cislo = $rodne_cislo;
    }

    public function createComponentAddKeeper()
    {
        $model = new KeeperModel($this->database);
        $form = $this->form();
        $form->addText('jmeno', 'Jméno: ')
            ->setRequired("Jméno je povinný údaj.");
        $form->addText('prijmeni', 'Příjmení: ')
            ->setRequired("Příjmení je povinný údaj.");
        $form->addText('rodne_cislo', 'Rodné číslo: ')
            ->addRule(MyValidation::RODNECISLO, 'Zadejte platné rodné číslo.')
            ->setRequired("Rodné číslo je povinný údaj.");
        $sex = ['M' => 'muž', 'Z' => 'žena'];
        $form->addRadioList('pohlavi', 'Pohlaví:', $sex)
            ->setRequired("Pohlaví je povinný údaj.");
        $form->addText('datum_narozeni', "Datum narození:")
            ->setRequired("Datum narození je povinný údaj")
            ->setAttribute("class", "dtpicker col-sm-2")
            ->setAttribute('placeholder', 'YYYY-MM-DD')
            ->addRule(MyValidation::DATUM, "Datum musí být ve formátu YYYY-MM-DD");
        $form->addText('tel_cislo', 'Telefoní číslo: ')
            ->setRequired("Telefoní číslo je povinný údaj.");
        $form->addText('adresa', 'Bydliště: ')
            ->setRequired("Adresa je povinný údaj.");
        $form->addText('titul', 'Tituly: ');
        $form->addText('login', 'Uživatelské jméno:')
            ->setRequired("Login je povinný údaj.");
        $form->addText('datum_nastupu', 'Datum nástupu:')
            ->setAttribute('placeholder', 'YYYY-MM-DD')
            ->setRequired(false)
            ->addRule(MyValidation::DATUM, "Datum musí být ve formátu YYYY-MM-DD");


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

        try {

            $rr = mb_substr($values->rodne_cislo, 0, 2);
            $mm = mb_substr($values->rodne_cislo, 2,2);
            $dd = mb_substr($values->rodne_cislo, 4, 2);

            $rrDate = mb_substr($values->datum_narozeni, 2,2);
            $mmDate = mb_substr($values->datum_narozeni, 5, 2);
            $ddDate = mb_substr($values->datum_narozeni, 8,2);

            if($rr === $rrDate && $dd === $ddDate &&
                ($mm === $mmDate || '0'.(string)($mm - 50) === $mmDate ||
                    '0'.(string) ($mm-20) === $mmDate || '0'.(string) ($mm - 70) === $mmDate))
            {
                $model = new KeeperModel($this->database);
                if ($values->role == 0) {
                    $model->addKeeper($values);
                } else if ($values->role == 1) {
                    $model->addKeeperEmployee($values);
                } else {
                    $model->addKeeperVolunteer($values);
                }



                $this->flashMessage('Záznam přidán!' ,'success');
                $this->redirect('Keeper:add');
            }
            else
            {
                $form['datum_narozeni']->addError("Datum narození se neshoduje s rodným číslem.");
            }


        }
        catch (Nette\Database\UniqueConstraintViolationException $e)
        {

            if (strpos($values->login, $e->getMessage()) === false)
                $form['login']->addError('Tento login je již přidělen');

            if (strpos($values->rodne_cislo, $e->getMessage()) === false)
                $form['rodne_cislo']->addError('Toto rodne čislo je již jednou vložené.');

        }


    }


    public function createComponentUpdateKeeper()
    {
        $form = $this->form();

        $model = new KeeperModel($this->database);
        $values = $model->getKeeperValues($this->rodne_cislo);

        $form->addText('login', $values['login'])
            ->setRequired("Login je povinný údaj.")->setDefaultValue($values['login']);
        $form->addText('jmeno', 'Jméno: ')
            ->setRequired("Jméno je povinný údaj.")->setDefaultValue($values['jmeno']);
        $form->addText('prijmeni', 'Příjmení: ')
            ->setRequired("Příjmení je povinný údaj.")->setDefaultValue($values['prijmeni']);
        $form->addhidden('rodne_cislo', 'Rodné číslo: ')
            ->setRequired("Rodné číslo je povinný údaj.")->setDefaultValue($values['rodne_cislo']);
        $sex = ['M' => 'muž', 'Z' => 'žena'];
        $form->addRadioList('pohlavi', 'Pohlaví:', $sex)
            ->setRequired("Pohlaví je povinný údaj.")->setDefaultValue($values['pohlavi']);
        $form->addText('datum_narozeni', "Datum narození:")
            ->setRequired("Datum narození je povinný údaj")
            ->setDefaultValue(substr($values['datum_narozeni'],0,10))
            ->setAttribute("class", "dtpicker col-sm-2")
            ->setAttribute('placeholder', 'YYYY-MM-DD')
            ->addRule(MyValidation::DATUM, "Datum musí být ve formátu YYYY-MM-DD");
        $form->addText('tel_cislo', 'Telefoní číslo: ')
            ->setRequired("Telefoní číslo je povinný údaj.")->setDefaultValue($values['tel_cislo']);
        $form->addText('adresa', 'Bydliště: ')
            ->setRequired("Adresa je povinný údaj.")->setDefaultValue($values['adresa']);
        $form->addText('titul', 'Tituly: ')
            ->setDefaultValue($values['titul']);

        $role = ['0' => 'Admin', '1' => 'Zaměstnanec', '2' => 'Dobrovolník'];
        $form->addRadioList('role', 'Typ zařadění: ', $role)
            ->setDisabled(true)
            ->setDefaultValue($values['role'])
            ->addCondition($form::EQUAL, '1')
            ->toggle('mzda')
            ->toggle('specializace')
            ->toggle('pozice')
            ->endCondition()
            ->addCondition($form::EQUAL, '2')
            ->toggle('organizace')
            ->toggle('zodpovedna_osoba');

        //Deleted in next function. Just for choose a function to call
        $form->addHidden('roleToChoose')->setDefaultValue($values['role']);

        //Zaměstanec
        $employeeValues =  $model->getEmployeeKeeperValues($this->rodne_cislo);
        $form->addText('mzda', 'Mzda: ')->setDefaultValue($employeeValues['mzda'])
            ->setOption('id', 'mzda');
        $form->addText('specializace', 'Specializace: ')->setDefaultValue($employeeValues['specializace'])
            ->setOption('id', 'specializace');
        $form->addText('pozice', 'Pozice: ')->setDefaultValue($employeeValues['pozice'])
            ->setOption('id', 'pozice');


        //Dobrovolnik

        $volunteerValues =  $model->getVolunteerKeeperValues($this->rodne_cislo);
        $form->addText('organizace', 'Organizace: ')->setDefaultValue($volunteerValues['organizace'])
            ->setOption('id', 'organizace');
        $form->addSelect('zodpovedna_osoba', 'Zodpovědná osoba: ', $model->getRodneCisloByLogin())
            ->setDefaultValue($volunteerValues['zodpovedna_osoba'])
            ->setOption('id', 'zodpovedna_osoba');


        $form->addSubmit('submit', 'Upravit');
        $form->onSuccess[] = [$this, 'updateKeeperSucceed'];
        return $form;

    }

    public function updateKeeperSucceed(Form $form, Nette\Utils\ArrayHash $values){

        try{
            $model = new KeeperModel($this->database);
            if($values['roleToChoose'] == 0){
                unset($values['roleToChoose']);
                $model->updateKeeper($values);
            } else if($values['roleToChoose'] == 1) {
                unset($values['roleToChoose']);
                $model->updateKeeperEmployee($values);
            } else {
                unset($values['roleToChoose']);
                $model->updateKeeperVolunteer($values);
            }

            $this->flashMessage('Ošetřovatel upraven!', 'success');
            $this->redirect('Keeper:search');

        }
        catch (Nette\Database\UniqueConstraintViolationException $e)
        {

            if (strpos($values->login, $e->getMessage()) === false)
                $form['login']->addError('Tento login je již přidělen');

            if (strpos($values->rodne_cislo, $e->getMessage()) === false)
                $form['rodne_cislo']->addError('Toto rodne čislo je již jednou vložené.');

        }

    }



    public function createComponentSearchKeeper(){
        $form = $this->form();

        $form->addText('login', 'Login: ');

        $form->addText('jmeno', 'Jméno ošetřovatele: ');

        $form->addText('prijmeni', 'Příjmení ošetřovatele: ');


        $form->addSubmit('submit', 'Vyhledat ošetřovatele');
        $form->onSuccess[] = [$this, 'renderSearchKeeperSucceed'];
        return $form;
    }

    public function renderSearchKeeperSucceed(Nette\Application\UI\Form $form){
        $this->template->data = $this->model->searchKeeper($form->getValues(true));
        $this->template->show = true;
    }


}