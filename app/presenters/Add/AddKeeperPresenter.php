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
        $form->addText('datum_narozeni', "Datum:")
            ->setRequired("Datum narození je povinný údaj")
            ->setAttribute("class", "dtpicker col-sm-2")
            ->setAttribute('placeholder', 'rrrr.mm.dd')
            ->addRule($form::PATTERN, "Datum musí být ve formátu YYYY.MM.DD", "(19|20)\d\d\.(0[1-9]|1[012])\.(0[1-9]|[12][0-9]|r[01])");
        $form->addText('tel_cislo', 'Telefoní číslo: ')
            ->setRequired();
        $form->addText('adresa', 'Bydliště: ')
            ->setRequired();
        $form->addText('titul', 'Tituly: ')
            ->setRequired();
        $form->addText('login', 'Uživatelské jméno:')
            ->setRequired();
        $form->addText('heslo', 'Heslo:')
            ->setRequired();

        $role = ['Z' => 'zaměstnanec', 'D' => 'dobrovolník'];
        $form->addRadioList('role', 'Typ zařadění: ', $role)
            ->addCondition($form::EQUAL, 'Z')
                ->toggle('address-streets')
                ->toggle('address-zipcode')
            ->endCondition()
            ->addCondition($form::EQUAL, 'D')
                ->toggle('address');
        $form['role']->setDefaultValue('Z');


        $form->addText('street', 'prvni')
            ->setOption('id', 'address-streets');
        $form->addText('zipcode', 'druhy')
            ->setOption('id', 'address-zipcode');
        $form->addText('sret', 'treti')
            ->setOption('organizace', 'organizace');



        $form->addSubmit('submit', 'Přidat');
        $form->onSuccess[] = [$this, 'addKeeperSucceed'];
        return $form;

    }

    public function addKeeperSucceed(Form $form, Nette\Utils\ArrayHash $values)
    {
        $model = new KeeperModel($this->database);
        $model->addKeeper($form->getValues(true));
        $this->flashMessage('Ošetřovatel přidán!' ,'success');
        $this->redirect('AddKeeper:');

    }

}