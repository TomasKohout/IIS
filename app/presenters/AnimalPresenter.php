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
    protected $model;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
        $this->model = new AnimalModel($database);
    }

    protected function startup(){
        parent::startup();

        if (!$this->getUser()->isAllowed('animal', 'view')){
            $this->flashMessage('Pro přístup na tuto stránku nemáte oprávnění. Obraťte se prosím na administrátora.', 'warning');
            $this->redirect('MainPage:default');
        }
    }
    public function renderAdd()
    {
        if (!$this->getUser()->isAllowed('animal', 'add')){
            $this->flashMessage('Pro přístup na tuto stránku nemáte oprávnění. Obraťte se prosím na administrátora.', 'warning');
            $this->redirect('MainPage:default');
        }
    }

    public function renderSearch()
    {
        $this->template->dataAll = $this->model->allAnimals();
        $this->template->druh = $this->model->getDruh();
    }
    public function renderUpdate($id_zvire){
        if (!$this->getUser()->isAllowed('animal', 'add')){
            $this->flashMessage('Pro přístup na tuto stránku nemáte oprávnění. Obraťte se prosím na administrátora.', 'warning');
            $this->redirect('MainPage:default');
        }
        $this->id_zvire = $id_zvire;
    }
    public function renderUmrti($id_zvire){
        if (!$this->getUser()->isAllowed('animal', 'add')){
            $this->flashMessage('Pro přístup na tuto stránku nemáte oprávnění. Obraťte se prosím na administrátora.', 'warning');
            $this->redirect('MainPage:default');
        }
    }

    public function createComponentDeadAnimal(){
        $form = $this->form();
        $model = new AnimalModel($this->database);

        $form->addSelect('id_zvire', 'Vyber zvíře:', $model->getZvire())
            ->setPrompt('Zvol zvíře')
            ->setRequired('Zvol zvíře!');
        $form->addText('datum_umrti', "Datum:")
            ->setRequired("Datum úmrtí je povinný údaj")
            ->setAttribute("class", "dtpicker col-sm-2")
            ->setAttribute('placeholder', 'rrrr.mm.dd')
            ->addRule($form::PATTERN, "Datum musí být ve formátu YYYY.MM.DD", "(19|20)\d\d\.(0[1-9]|1[012])\.(0[1-9]|[12][0-9]|r[01])");

        $form->addSubmit('submit', 'Upravit zvíře')
            ->setAttribute('id', 'confirm');

        $form->onSuccess[] = [$this, 'deadAnimalSucceed'];

        return $form;
    }

    public function deadAnimalSucceed(Form $form, Nette\Utils\ArrayHash $arrayHash){
        $values = $form->getValues(true);
        $row = $this->model->getAnimalValues($values['id_zvire']);
        $death = strtotime($values['datum_umrti']);
        $birth = strtotime($row['datum_narozeni']);

        if ($death > $birth)
        {
            $this->updateAnimalSucceed($form, $arrayHash);
        }
        else
        {
           $this->flashMessage('Zvíře nesmí zemřít dřív než se narodilo!', 'danger');
           $this->redirect('Animal:umrti');
        }

    }

    public function createComponentUpdateAnimal(){
        $form = $this->form();
        $model = new AnimalModel($this->database);
        $values = $model->getAnimalValues($this->id_zvire);

        $sex = ['M' => 'muž', 'Z' => 'žena'];
        $form->addHidden('id_zvire',$values['id_zvire']);
        $form->addText('jmeno', 'Jméno:')
            ->setDefaultValue($values['jmeno'])
            ->setRequired('Jméno je povinný údaj.');
        $form->addSelect('jeDruhu', 'Druh:', $model->getDruh())
            ->setDefaultValue($values['jeDruhu'])
            ->setRequired('Druh je povinný údaj.');
        $form->addRadioList('pohlavi', 'Pohlaví:', $sex)
            ->setDefaultValue($values['pohlavi'])
            ->setRequired('Pohlaví je povinný údaj.');
        $form->addText('vaha', 'Váha:')
            ->setDefaultValue($values['vaha'])
            ->setRequired('Váha je povinný údaj.');
        $form->addText('vyska', 'Výška:')
            ->setDefaultValue($values['vyska'])
            ->setRequired('Výška je povinný údaj.');
        $form->addText('jmeno_matky', 'Jméno matky:')
            ->setDefaultValue($values['jmeno_matky'])
            ->setRequired('Jméno matky je povinný údaj.');
        $form->addText('jmeno_otce', 'Jméno otce:')
            ->setDefaultValue($values['jmeno_otce'])
            ->setRequired('Jméno otce je povinný údaj.');
        $form->addSelect('obyva', 'Výběh číslo:', $model->getTypVybehu())
            ->setDefaultValue($values['obyva'])
            ->setPrompt('Vybeh')
            ->setRequired('Výběh je povinný údaj.');
        $form->addSelect('zeme_puvodu', 'Země původu:', $this->getCountries())
            ->setDefaultValue($values['zeme_puvodu'])
            ->setRequired('Země původu je povinný údaj.');
        $form->addText('datum_narozeni', "Datum:")
            ->setDefaultValue(substr($values['datum_narozeni'],0,10))
            ->setRequired("Datum narození je povinný údaj")
            ->setAttribute("class", "dtpicker col-sm-2")
            ->setAttribute('placeholder', 'rrrr.mm.dd')
            ->addRule($form::PATTERN, "Datum musí být ve formátu YYYY.MM.DD", "(19|20)\d\d\.(0[1-9]|1[012])\.(0[1-9]|[12][0-9]|r[01])");

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
            ->setRequired('Jméno je povinný údaj.');
        $form->addSelect('jeDruhu', 'Druh:', $model->getDruh())
            ->setPrompt('Zvol druh')
            ->setRequired('Druh je povinný údaj.');
        $form->addRadioList('pohlavi', 'Pohlaví:', $sex)
            ->setRequired('Pohlaví je povinný údaj.');
        $form->addText('vaha', 'Váha:')
            ->setHtmlType('number')
            ->setRequired('Váha je povinný údaj.');
        $form->addText('vyska', 'Výška:')
            ->setHtmlType('number')
            ->setRequired('Výška je povinný údaj.');
        $form->addText('jmeno_matky', 'Jméno matky:')
            ->setRequired('Jméno matky je povinný údaj.');
        $form->addText('jmeno_otce', 'Jméno otce:')
            ->setRequired('Jméno otce je povinný údaj.');
        $form->addSelect('obyva', 'Výběh číslo:', $model->getTypVybehu())
            ->setPrompt('Vybeh')
            ->setRequired('Výběh je povinný údaj.');
        $form->addSelect('zeme_puvodu', 'Země původu:',$this->getCountries())
            ->setPrompt('Zvol zemi')
            ->setRequired('Země původu je povinný údaj.');
        $form->addText('datum_narozeni', "Datum:")
            ->setRequired("Datum narození je povinný údaj")
            ->setAttribute("class", "dtpicker col-sm-2")
            ->setAttribute('placeholder', 'rrrr-mm-dd')
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
        $model = new AnimalModel($this->database);
        $form = $this->form();
        $form->addText('jmeno', 'Jméno zvířete: ');
        $form->addSelect('jeDruhu', 'Druh zvířete: ', $model->getDruh())
            ->setPrompt('Zvol druh');;

        $form->addSubmit('submit', 'Vyhledat zvíře');
        $form->onSuccess[] = [$this, 'renderSearchAnimalSucceed'];
        return $form;
    }

    public function renderSearchAnimalSucceed(Nette\Application\UI\Form $form){
        $this->template->data = $this->model->searchAnimal($form->getValues(true));
        //$this->template->druh = $this->model->getDruh();
        $this->template->showAnimals = true;
    }
}