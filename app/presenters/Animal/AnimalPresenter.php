<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 19.10.17
 * Time: 11:51
 */

namespace App\Presenters;
use App\Model\AnimalModel;
use Nette;
use Nette\Application\UI\Form;
use Nextras;

class AnimalPresenter extends BasePresenter
{
    protected $database;
    protected $id_zvire;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function renderAdd()
    {

    }

    public function renderSearch()
    {

    }
    public function renderUpdate($id_zvire){
        $this->id_zvire = $id_zvire;
    }
    public function renderUmrti($id_zvire){

    }

    public function createComponentDeadAnimal(){
        $form = $this->form();
        $model = new AnimalModel($this->database);

        $form->addSelect('id_zvire', 'Vyber zvíře:', $model->getZvire())
            ->setPrompt('Zvol zvíře');
        $form->addText('datum_umrti', "Datum:")
            ->setRequired("Datum úmrtí je povinný údaj")
            ->setAttribute("class", "dtpicker col-sm-2")
            ->setAttribute('placeholder', 'rrrr.mm.dd')
            ->addRule($form::PATTERN, "Datum musí být ve formátu YYYY.MM.DD", "(19|20)\d\d\.(0[1-9]|1[012])\.(0[1-9]|[12][0-9]|r[01])");

        $form->addSubmit('submit', 'Upravit zvíře');

        $form->onSuccess[] = [$this, 'updateAnimalSucceed'];

        return $form;
    }

    public function createComponentUpdateAnimal(){
        $form = $this->form();
        $model = new AnimalModel($this->database);
        $values = $model->getAnimalValues($this->id_zvire);

        $sex = ['M' => 'muž', 'Z' => 'žena'];
        $form->addHidden('id_zvire',$values['id_zvire']);
        $form->addText('jmeno', 'Jméno:')
            ->setDefaultValue($values['jmeno']);
        $form->addSelect('jeDruhu', 'Druh:', $model->getDruh())
            ->setDefaultValue($values['jeDruhu']);
        $form->addRadioList('pohlavi', 'Pohlaví:', $sex)
            ->setDefaultValue($values['pohlavi'])
            ->setRequired();
        $form->addText('vaha', 'Váha:')
            ->setDefaultValue($values['vaha'])
            ->setRequired();
        $form->addText('vyska', 'Výška:')
            ->setDefaultValue($values['vyska'])
            ->setRequired();
        $form->addText('jmeno_matky', 'Jméno matky:')
            ->setDefaultValue($values['jmeno_matky'])
            ->setRequired();
        $form->addText('jmeno_otce', 'Jméno otce:')
            ->setDefaultValue($values['jmeno_otce'])
            ->setRequired();
        $form->addSelect('obyva', 'Výběh číslo:', $model->getTypVybehu())
            ->setDefaultValue($values['obyva'])
            ->setPrompt('Vybeh');
        $form->addSelect('zeme_puvodu', 'Země původu:', $this->getCountries())
            ->setDefaultValue($values['zeme_puvodu']);
        $form->addText('datum_narozeni', "Datum:")
            ->setDefaultValue(substr($values['datum_narozeni'],0,10))
            ->setRequired("Datum narození je povinný údaj")
            ->setAttribute("class", "dtpicker col-sm-2")
            ->setAttribute('placeholder', 'rrrr.mm.dd')
            ->addRule($form::PATTERN, "Datum musí být ve formátu YYYY-MM-DD", "(19|20)\d\d\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|r[01])");

        $form->addSubmit('submit', 'Upravit zvíře');

        $form->onSuccess[] = [$this, 'updateAnimalSucceed'];

        return $form;
    }

    public function updateAnimalSucceed(Form $form, Nette\Utils\ArrayHash $array){
        $model = new AnimalModel($this->database);
        $model->updateAnimal($form->getValues(true));
        $this->flashMessage('Zvíře upraveno!', 'success');
        $this->redirect('Animal:search');
    }


    public function createComponentAddAnimal()
    {
        $model = new AnimalModel($this->database);
        $sex = ['M' => 'muž', 'Z' => 'žena'];
        $form = $this->form();
        $form->addText('jmeno', 'Jméno zvířete: ')
            ->setRequired();
        $form->addSelect('jeDruhu', 'Druh:', $model->getDruh())
            ->setPrompt('Zvol druh');
        $form->addRadioList('pohlavi', 'Pohlaví:', $sex)
            ->setRequired();
        $form->addText('vaha', 'Váha:')
            ->setHtmlType('number')
            ->setRequired();
        $form->addText('vyska', 'Výška:')
            ->setHtmlType('number')
            ->setRequired();
        $form->addText('jmeno_matky', 'Jméno matky:')
            ->setRequired();
        $form->addText('jmeno_otce', 'Jméno otce:')
            ->setRequired();
        $form->addSelect('obyva', 'Výběh číslo:', $model->getTypVybehu())
            ->setPrompt('Vybeh');
        $form->addSelect('zeme_puvodu', 'Země původu:',$this->getCountries())
            ->setPrompt('Zvol zemi');
        $form->addText('datum_narozeni', "Datum:")
            ->setRequired("Datum narození je povinný údaj")
            ->setAttribute("class", "dtpicker col-sm-2")
            ->setAttribute('placeholder', 'rrrr.mm.dd')
            ->addRule($form::PATTERN, "Datum musí být ve formátu YYYY.MM.DD", "(19|20)\d\d\.(0[1-9]|1[012])\.(0[1-9]|[12][0-9]|r[01])");

        $form->addSubmit('submit', 'Přidat');
        $form->onSuccess[] = [$this, 'addAnimalSucceed'];
        return $form;

    }

    public function addAnimalSucceed(Form $form, Nette\Utils\ArrayHash $values)
    {
        $model = new AnimalModel($this->database);
        $model->addAnimal($form->getValues(true));
        $this->flashMessage('Zvíře přidáno!' ,'success');
        $this->redirect('Animal:add');

    }

    public function createComponentSearchAnimal(){
        $form = $this->form();
        $form->addText('jmeno', 'Jméno zvířete: ')
            ->setRequired('Jmeno');

        $form->addSubmit('submit', 'Vyhledat zvíře');
        $form->onSuccess[] = [$this, 'renderAnimalSucceed'];
        return $form;
    }

    public function renderAnimalSucceed(Form $form){
        $model = new AnimalModel($this->database);
        $this->template->data = $model->searchAnimal($form->getValues(true));
        $this->template->druh = $model->getDruh();
        $this->template->showAnimals = true;
    }
}