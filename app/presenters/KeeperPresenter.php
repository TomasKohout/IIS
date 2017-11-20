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

    public function renderAdd(){

    }

    public function renderSearch(){
        $this->template->dataAll = $this->model->allKeeper();

    }

    public function renderUpdate($rodne_cislo){
        $this->rodne_cislo = $rodne_cislo;
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
        $this->redirect('Keeper:');

    }


    public function createComponentUpdateKeeper()
    {
        $form = $this->form();

        $model = new KeeperModel($this->database);
        $values = $model->getKeeperValues($this->rodne_cislo);

        $form->addText('login', $values['login'])
            ->setRequired()->setDefaultValue($values['login']);
        $form->addText('jmeno', 'Jméno: ')
            ->setRequired()->setDefaultValue($values['jmeno']);
        $form->addText('prijmeni', 'Příjmení: ')
            ->setRequired()->setDefaultValue($values['prijmeni']);
        $form->addhidden('rodne_cislo', 'Rodné číslo: ')
            ->setRequired()->setDefaultValue($values['rodne_cislo']);
        $sex = ['M' => 'muž', 'Z' => 'žena'];
        $form->addRadioList('pohlavi', 'Pohlaví:', $sex)
            ->setRequired()->setDefaultValue($values['pohlavi']);
        $form->addText('datum_narozeni', "Datum narození:")
            ->setRequired("Datum narození je povinný údaj")
            ->setDefaultValue($values['datum_narozeni'])
            ->setAttribute("class", "dtpicker col-sm-2")
            ->setAttribute('placeholder', 'rrrr.mm.dd')
            ->addRule($form::PATTERN, "Datum musí být ve formátu YYYY.MM.DD", "(19|20)\d\d\.(0[1-9]|1[012])\.(0[1-9]|[12][0-9]|r[01])");
        $form->addText('tel_cislo', 'Telefoní číslo: ')
            ->setRequired()->setDefaultValue($values['tel_cislo']);
        $form->addText('adresa', 'Bydliště: ')
            ->setRequired()->setDefaultValue($values['adresa']);
        $form->addText('titul', 'Tituly: ')
            ->setDefaultValue($values['titul']);


//        $mzda = "";
//        if(isset($values['mzda'])){
//            $mzda = $values['mzda'];
//        }
//        $specializace = "";
//        if(isset($values['specializace'])){
//            $specializace = $values['specializace'];
//        }
//        $pozice = "";
//        if(isset($values['pozice'])){
//            $pozice = $values['pozice'];
//        }

        //Zaměstanec
        if($values['role'] == 1) {
            $employeeValues =  $model->getEmployeeKeeperValues($this->rodne_cislo);
            $form->addText('mzda', 'Mzda: ')->setDefaultValue($employeeValues['mzda']);
            $form->addText('specializace', 'Specializace: ')->setDefaultValue($employeeValues['specializace']);
            $form->addText('pozice', 'Pozice: ')->setDefaultValue($employeeValues['pozice']);
        }

        //Dobrovolnik
        if($values['role'] == 2) {
            $volunteerValues =  $model->getVolunteerKeeperValues($this->rodne_cislo);
            $form->addText('organizace', 'Organizace: ')->setDefaultValue($volunteerValues['organizace']);
            $form->addSelect('zodpovedna_osoba', 'Zodpovědná osoba: ', $model->getRodneCisloByLogin())
                ->setDefaultValue($volunteerValues['zodpovedna_osoba']);
        }

        $form->addSubmit('submit', 'Přidat');
        $form->onSuccess[] = [$this, 'updateKeeperSucceed'];
        return $form;

    }

    public function updateKeeperSucceed(Form $form, Nette\Utils\ArrayHash $values){

        $model = new KeeperModel($this->database);
        if($values->role == 0){
            $model->updateKeeper($values);
        } else if($values->role == 1) {
            $model->updateKeeperEmployee($values);
        } else {
            $model->updateKeeperVolunteer($values);
        }

        $this->flashMessage('Ošetřovatel upraven!', 'success');
        $this->redirect('Keeper:search');
    }



    public function createComponentSearchKeeper(){
        $form = $this->form();
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