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
    protected $id_skoleni;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
        $this->model    = new TrainingModel($database);
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

    public function renderDelete($id_skoleni){
        $this->model->deleteTraining($id_skoleni);
        $this->flashMessage('Školení smazáno!', 'success');
        $this->redirect('Training:show');

    }

    public function renderUpdate($id_skoleni){
        $this->id_skoleni = $id_skoleni;
    }


    public function renderSearch(){
        $this->template->dataAll = $this->model->getAllTrainings();
    }

    public function createComponentSearchTraining(){
        $form = $this->form();
        $form->addText("nazev", "Název:");

        $form->addSubmit('submit', "Hledat");
        $form->onSuccess[] = [$this, 'searchTrainingSucceed'];

        return $form;
    }

    public function searchTrainingSucceed(Form $form){

        $this->template->data = $this->model->searchTrainings($form->getValues(true));
        $this->template->show = true;
    }

    public function createComponentAddSkoleni(){
        $form = $this->form();

        $form->addText('nazev', 'Název školení:')
            ->setDefaultValue('Název')
            ->addRule($form::MAX_LENGTH,'Název je příliš dlouhý. Maximální délka je %d.',30)
            ->setRequired('Název je povinný údaj.');

        $form->addText('datum', "Datum:")
            ->setRequired("Datum je povinný údaj")
            ->setAttribute("class", "dtpicker col-sm-2")
            ->setAttribute('placeholder', 'rrrr.mm.dd')
            ->addRule($form::PATTERN, "Datum musí být ve formátu YYYY.MM.DD", "(19|20)\d\d\.(0[1-9]|1[012])\.(0[1-9]|[12][0-9]|r[01])");

        $form->addTextArea('popis','Popis školení:', 2,2)
            ->setRequired(false);

        $form->addSubmit('submit', 'Přidat školení');


        $form->onSuccess[] = [$this, 'addSkoleniSucceed'];

        return $form;
    }

    public function addSkoleniSucceed(Form $form, Nette\Utils\ArrayHash $values){
        $model = new AnimalModel($this->database);
        $this->model->addTraining($form->getValues(true));

        $this->flashMessage('Školení přidáno!' ,'success');
        $this->redirect('Training:add');


    }

    public function createComponentUpdateTraining(){
        $form = $this->form();
        $row = $this->model->getTraining($this->id_skoleni);

        $form->addHidden('id_skoleni', $row['id_skoleni']);
        $form->addText('nazev', 'Název školení:')
            ->setDefaultValue($row['nazev'])
            ->addRule($form::MAX_LENGTH,'Název je příliš dlouhý. Maximální délka je %d.',30)
            ->setRequired('Název je povinný údaj.');

        $form->addText('datum', "Datum:")
            ->setRequired("Datum je povinný údaj")
            ->setAttribute("class", "dtpicker col-sm-2")
            ->setDefaultValue(substr($row['datum'],0,10))
            ->setAttribute('placeholder', 'rrrr.mm.dd')
            ->addRule($form::PATTERN, "Datum musí být ve formátu YYYY-MM-DD", "(19|20)\d\d\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|r[01])");

        $form->addTextArea('popis','Popis školení:', 2,2)
            ->setDefaultValue($row['popis'])
            ->setRequired(false);

        $form->addSubmit('submit', 'Upravit školení');



        $form->onSuccess[] = [$this, 'updateTrainingSucceed'];
        return $form;
    }

    public function updateTrainingSucceed(Form $form){

        $this->model->updateTraining($form->getValues(true));
        $this->flashMessage('Školení upraveno!', 'success');
        $this->redirect('Training:show');

    }
}