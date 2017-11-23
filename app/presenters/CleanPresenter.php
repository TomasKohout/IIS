<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 20.11.17
 * Time: 10:34
 */

namespace App\Presenters;
use Nette;
use App\Model\AnimalModel;
use App\Model\CleanModel;
use Nette\Application\UI\Form;
use Nextras;
use App\Forms\MyValidation;

class CleanPresenter extends BasePresenter
{

    protected $database;
    protected $model;
    protected $animalModel;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
        $this->model = new CleanModel($this->database);
        $this->animalModel = new AnimalModel($this->database);
    }

    protected function startup(){
        parent::startup();

        if (!$this->user->isAllowed('clean', 'add'))
        {
            $this->flashMessage('Pro přístup na tuto stránku nemáte oprávnění. Obraťte se prosím na administrátora.', 'warning');
            $this->redirect('MainPage:default');
        }
    }


    public function renderSearch(){
        $this->template->dataAll = $this->model->allClean();
        $this->template->druh = $this->animalModel->getZvire();
    }

    public function renderAdd(){

    }

    public function createComponentSearchClean(){

        $form = $this->form();
        $form->addText('jeCisten', 'ID výběhu: ');

        $form->addSubmit('submit', 'Vyhledat krmení');
        $form->onSuccess[] = [$this, 'searchCleanSucceed'];
        return $form;
    }

    public function searchCleanSucceed(Nette\Application\UI\Form $form){
        $this->template->data = $this->model->searchClean($form->getValues(true));
        $this->template->show = true;
    }

    public function createComponentAddClean()
    {

        $form = $this->form();
        $form->addText('jeCisten', 'ID výběhu: ')
            ->setRequired('Jméno je povinný údaj.');
        $form->addHidden('cas', "Datum:")
            ->setDefaultValue(StrFTime("%Y.%m.%d", Time()))
            ->setRequired("Datum a čas krmení je povinný údaj")
            ->setAttribute("class", "dtpicker col-sm-2")
            ->setAttribute('placeholder', 'YYYY-MM-DD')
            ->addRule(MyValidation::DATUM, "Datum musí být ve formátu YYYY-MM-DD");

        $form->addSubmit('submit', 'Přidat');
        $form->onSuccess[] = [$this, 'addCleanSucceed'];
        return $form;

    }

    public function addCleanSucceed(Form $form, Nette\Utils\ArrayHash $values)
    {
        $this->model->addClean($form->getValues(true));
        $this->flashMessage('Čištění přidáno!' ,'success');
        $this->redirect('Clean:add');

    }

}