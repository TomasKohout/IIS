<?php
namespace App\Presenters;
use App\Model\AnimalModel;
use App\Model\TrainingModel;
use Nette\Application\UI\Form;
use Nette;


class TrainingPresenter extends BasePresenter
{
    protected $database;
    protected $model;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
        $this->model    = new TrainingModel($database);
    }

    public function renderAdd(){

    }

    public function renderDelete($id_skoleni){

    }

    public function renderUpdate($id_skoleni){

    }
    public function renderShow(){
        $this->template->dataAll = $this->model->getAllTraining();
    }

    public function createComponentShowTraining(){
        $form = $this->form();
        return $form;
    }

    public function showTrainingSucceed(){

    }

    public function createComponentAddSkoleni(){
        $form = $this->form();

        $form->addText('nazev', 'Název školení:')
            ->setDefaultValue('Název')
            ->addRule($form::MAX_LENGTH,'Název je příliš dlouhý. Maximální délka je %d.',30)
            ->setRequired(true);

        $form->addText('datum', "Datum:")
            ->setRequired("Datum je povinný údaj")
            ->setAttribute("class", "dtpicker col-sm-2")
            ->setAttribute('placeholder', 'rrrr.mm.dd')
            ->addRule($form::PATTERN, "Datum musí být ve formátu YYYY.MM.DD", "(19|20)\d\d\.(0[1-9]|1[012])\.(0[1-9]|[12][0-9]|r[01])");

        $form->addTextArea('popis','Popis školení:', 2,2);

        $form->addSubmit('submit', 'Přidat školení');


        $form->onSuccess[] = [$this, 'addSkoleniSucceed'];

        return $form;
    }

    public function addSkoleniSucceed(Form $form, Nette\Utils\ArrayHash $values){
        $model = new AnimalModel($this->database);
        $this->model->addSkoleni($form->getValues(true));

        $this->flashMessage('Školení přidáno!' ,'success');
        $this->redirect('Training:add');


    }
}