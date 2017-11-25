<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 19.10.17
 * Time: 11:51
 */

namespace App\Presenters;
use App\Forms\MyValidation;
use App\Model\AnimalModel;
use App\Model\CoopModel;
use Nette;
use Nette\Application\UI\Form;
use Nextras;

class AnimalPresenter extends BasePresenter
{
    protected $database;
    protected $id_zvire;
    protected $animalModel;
    protected $coopModel;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
        $this->animalModel = new AnimalModel($database);
        $this->coopModel = new CoopModel($database);
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

    public function renderSearch($page = 1, $jmeno = null, $jeDruhu = null)
    {
        $paginator = new Nette\Utils\Paginator();
        $paginator->setItemsPerPage(10);
        $paginator->setPage($page);
        if ($jmeno != null || $jeDruhu != null)
        {

            $value = $this->removeEmpty(['jmeno'=> $jmeno,'jeDruhu' => $jeDruhu]);
            $animalCount = $this->animalModel->getCountOfAnimals($value);
            $paginator->setItemCount($animalCount);
            $this->template->data = $this->animalModel->allAnimals($paginator->getLength(), $paginator->getOffset(), $value);


            $this->template->jmeno = $jmeno;
            $this->template->jeDruhu = $jeDruhu;
            $this->template->showAnimals = true;
        }
        else
        {
            $animalCount = $this->animalModel->getCountOfAnimals();

            $paginator->setItemCount($animalCount);
            $this->template->dataAll = $this->animalModel->allAnimals($paginator->getLength(), $paginator->getOffset());
        }

        $this->template->druh = $this->animalModel->getDruh();
        $this->template->paginator = $paginator;
    }

    public function renderUpdate($id_zvire){
        if (!$this->getUser()->isAllowed('animal', 'add')){
            $this->flashMessage('Pro přístup na tuto stránku nemáte oprávnění. Obraťte se prosím na administrátora.', 'warning');
            $this->redirect('MainPage:default');
        }


        if ($this->animalModel->isDead($id_zvire)) {
            $this->flashMessage('Nelze upravovat zvířata, která jsou vedena jako mrtvá.', 'warning');
            $this->redirect('Animal:search');
        }


        $this->id_zvire = $id_zvire;
    }
    public function renderUmrti($id_zvire){
        if (!$this->getUser()->isAllowed('animal', 'add')){
            $this->flashMessage('Pro přístup na tuto stránku nemáte oprávnění. Obraťte se prosím na administrátora.', 'warning');
            $this->redirect('MainPage:default');
        }
        if ($this->animalModel->isDead($id_zvire)) {                                                                  
            $this->flashMessage('Nelze upravovat zvířata, která jsou vedena jako mrtvá.', 'warning');                 
            $this->redirect('Animal:search');
        }
        $this->id_zvire = $id_zvire;
    }

    public function createComponentDeadAnimal(){
        $form = $this->form();

        $form->addHidden('id_zvire')->setDefaultValue($this->id_zvire);
        $form->addHidden('obyva')->setDefaultValue(NULL);

        $form->addText('datum_umrti', "Datum:")
            ->setDefaultValue(StrFTime("%Y-%m-%d", Time()))
            ->setRequired("Datum úmrtí je povinný údaj")
            ->setAttribute("class", "dtpicker col-sm-2")
            ->setAttribute('placeholder', 'YYYY-MM-DD, povinný údaj')
            ->addRule(MyValidation::DATUM, "Datum musí být ve formátu YYYY-MM-DD");

        $form->addSubmit('submit', 'Upravit zvíře');

        $form->onSuccess[] = [$this, 'deadAnimalSucceed'];

        return $form;
    }

    public function deadAnimalSucceed(Form $form, Nette\Utils\ArrayHash $arrayHash){
        $values = $form->getValues(true);
        $row = $this->animalModel->getAnimalValues($values['id_zvire']);
        $death = strtotime($values['datum_umrti']);
        $birth = strtotime($row['datum_narozeni']);

        if ($death > $birth)
        {
            $this->animalModel->killAnimal($values);
            $this->flashMessage('Zvíře umrtveno!', 'success');
            $this->redirect('Animal:search');
        }
        else
        {
           $form['datum_umrti']->addError('Zvíře nemůže zemřít dříve než se narodilo.');
        }

    }

    public function createComponentUpdateAnimal(){
        $form = $this->form();
        $model = new AnimalModel($this->database);
        $values = $model->getAnimalValues($this->id_zvire);

        $sex = ['M' => 'muž', 'Z' => 'žena'];
        $form->addHidden('id_zvire',$values['id_zvire']);
        $form->addText('jmeno', 'Jméno:')
            ->setAttribute('placeholder', 'Povinný údaj')
            ->setDefaultValue($values['jmeno'])
            ->setRequired('Jméno je povinný údaj.');
        $form->addSelect('jeDruhu', 'Druh:', $model->getDruh())
            ->setDefaultValue($values['jeDruhu'])
            ->setAttribute('placeholder', 'Povinný údaj')
            ->setRequired('Druh je povinný údaj.');
        $form->addRadioList('pohlavi', 'Pohlaví:', $sex)
            ->setDefaultValue($values['pohlavi'])
            ->setRequired('Pohlaví je povinný údaj.');
        $form->addText('vaha', 'Váha:')
            ->setHtmlType('number')
            ->setDefaultValue($values['vaha'])
            ->setRequired(false);
        $form->addText('vyska', 'Výška:')
            ->setHtmlType('number')
            ->setDefaultValue($values['vyska'])
            ->setRequired(false);
        $form->addText('jmeno_matky', 'Jméno matky:')
            ->setDefaultValue($values['jmeno_matky'])
            ->setRequired(false);
        $form->addText('jmeno_otce', 'Jméno otce:')
            ->setDefaultValue($values['jmeno_otce'])
            ->setRequired(false);
        $form->addSelect('obyva', 'Výběh číslo:', $model->getTypVybehu())
            ->setDefaultValue($values['obyva'])
            ->setPrompt('Vybeh')
            ->setAttribute('placeholder', 'Povinný údaj')
            ->setRequired('Výběh je povinný údaj.');
        $form->addSelect('zeme_puvodu', 'Země původu:', $this->getCountries())
            ->setDefaultValue($values['zeme_puvodu'])
            ->setRequired(false);
        $form->addText('datum_narozeni', "Datum narození:")
            ->setDefaultValue(substr($values['datum_narozeni'],0,10))
            ->setRequired("Datum narození je povinný údaj")
            ->setAttribute("class", "dtpicker col-sm-2")
            ->setAttribute('placeholder', 'YYYY-MM-DD, povinný údaj')
            ->addRule(MyValidation::DATUM, "Datum musí být ve formátu YYYY-MM-DD");

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
            ->setAttribute('placeholder', 'Povinný údaj')
            ->setRequired('Jméno je povinný údaj.');
        $form->addSelect('jeDruhu', 'Druh:', $model->getDruh())
            ->setPrompt('Zvol druh')
            ->setAttribute('placeholder', 'Povinný údaj')
            ->setRequired('Druh je povinný údaj.');
        $form->addRadioList('pohlavi', 'Pohlaví:', $sex)
            ->setRequired('Pohlaví je povinný údaj.');
        $form->addText('vaha', 'Váha:')
            ->setHtmlType('number')
            ->setRequired(false);
        $form->addText('vyska', 'Výška:')
            ->setHtmlType('number')
            ->setRequired(false);
        $form->addText('jmeno_matky', 'Jméno matky:')
            ->setRequired(false);
        $form->addText('jmeno_otce', 'Jméno otce:')
            ->setRequired(false);
        $form->addSelect('obyva', 'Výběh číslo:', $model->getTypVybehu())
            ->setPrompt('Povinný údaj')
            ->setRequired('Výběh je povinný údaj.');
        $form->addSelect('zeme_puvodu', 'Země původu:',$this->getCountries())
            ->setPrompt('Zvol zemi')
            ->setRequired(false);
        $form->addText('datum_narozeni', "Datum narození:")
            ->setRequired("Datum narození je povinný údaj")
            ->setAttribute("class", "dtpicker col-sm-2")
            ->setAttribute('placeholder', 'YYYY-MM-DD, povinný údaj')
            ->addRule(MyValidation::DATUM, "Datum musí být ve formátu YYYY-MM-DD");

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
        $form->onSuccess[] = [$this, 'searchAnimalSucceed'];
        return $form;
    }

    public function searchAnimalSucceed(Nette\Application\UI\Form $form , Nette\Utils\ArrayHash $values){
        $this->redirect('Animal:search',1, $values->jmeno, (int)$values->jeDruhu);
    }
}