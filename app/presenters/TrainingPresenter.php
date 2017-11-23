<?php
namespace App\Presenters;
use App\Forms\MyValidation;
use App\Model\AnimalModel;
use App\Model\KeeperModel;
use App\Model\TrainingModel;
use Nette\Application\UI\Form;
use Nette;


class TrainingPresenter extends BasePresenter
{
    protected $database;
    protected $trainingModel;
    protected $id_skoleni;
    protected $keeperModel;
    /**
     * @persistent
     * @var int
     */
    public $id_keeper;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
        $this->trainingModel    =   new TrainingModel($database);
        $this->keeperModel      =   new KeeperModel($database);
    }

    protected function startup(){
        parent::startup();

    }

    public function renderAdd(){
        if (!$this->user->isAllowed('admin'))
        {
            $this->flashMessage('Pro přístup na tuto stránku nemáte oprávnění. Obraťte se prosím na administrátora.', 'warning');
            $this->redirect('MainPage:default');
        }
    }

    public function renderDelete($id_skoleni){
        if (!$this->user->isAllowed('admin'))
        {
            $this->flashMessage('Pro přístup na tuto stránku nemáte oprávnění. Obraťte se prosím na administrátora.', 'warning');
            $this->redirect('MainPage:default');
        }
        $this->trainingModel->isValidID($id_skoleni);
        $this->trainingModel->deleteTraining($id_skoleni);
        $this->flashMessage('Školení smazáno!', 'success');
        $this->redirect('Training:show');

    }

    public function renderUpdate($id_skoleni){
        if (!$this->user->isAllowed('admin'))
        {
            $this->flashMessage('Pro přístup na tuto stránku nemáte oprávnění. Obraťte se prosím na administrátora.', 'warning');
            $this->redirect('MainPage:default');
        }
        $this->trainingModel->isValidID($id_skoleni);
        $this->id_skoleni = $id_skoleni;
    }


    public function renderSearch(){
        if (!$this->user->isAllowed('training', 'view'))
        {
            $this->flashMessage('Pro přístup na tuto stránku nemáte oprávnění. Obraťte se prosím na administrátora.', 'warning');
            $this->redirect('MainPage:default');
        }
        $this->template->dataAll = $this->trainingModel->getAllTrainings();
    }

    public function renderAddTrainingToKeeper($id_keeper){
        if (!$this->user->isAllowed('admin'))
        {
            $this->flashMessage('Pro přístup na tuto stránku nemáte oprávnění. Obraťte se prosím na administrátora.', 'warning');
            $this->redirect('MainPage:default');
        }

        $this->keeperModel->isValidRodneCislo($id_keeper);

    }

    public function createComponentAddTrainingToKeeper(){
        $form = $this->form();
        $form->addSelect('id_skoleni', 'Školení: ', $this->trainingModel->getTrainings());
        $form->addHidden('rd_osetrovatel', $this->id_keeper);
        $form->addSubmit('submit','Udělit školení');
        $form->onSuccess[] = [$this, 'addTrainingToKeeperSucceed'];
        return $form;
    }

    public function addTrainingToKeeperSucceed(Form $form){
        $this->trainingModel->addTrainingToKeeper($form->getValues(true));

        $this->flashMessage('Školení úspěšně přidáno.', 'success');
        $this->redirect('Keeper:search');
    }
    public function createComponentSearchTraining(){
        $form = $this->form();
        $form->addText("nazev", "Název:");

        $form->addSubmit('submit', "Hledat");
        $form->onSuccess[] = [$this, 'searchTrainingSucceed'];

        return $form;
    }

    public function searchTrainingSucceed(Form $form){

        $this->template->data = $this->trainingModel->searchTrainings($form->getValues(true));
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
            ->setAttribute('placeholder', 'rrrr-mm-dd')
            ->addRule(MyValidation::DATUM, "Datum musí být ve formátu YYYY-MM-DD");

        $form->addTextArea('popis','Popis školení:', 2,2)
            ->setRequired(false);

        $form->addSubmit('submit', 'Přidat školení');


        $form->onSuccess[] = [$this, 'addSkoleniSucceed'];

        return $form;
    }

    public function addSkoleniSucceed(Form $form, Nette\Utils\ArrayHash $values){
        $model = new AnimalModel($this->database);
        $this->trainingModel->addTraining($form->getValues(true));

        $this->flashMessage('Školení přidáno!' ,'success');
        $this->redirect('Training:add');


    }

    public function createComponentUpdateTraining(){
        $form = $this->form();
        $row = $this->trainingModel->getTraining($this->id_skoleni);

        $form->addHidden('id_skoleni', $row['id_skoleni']);
        $form->addText('nazev', 'Název školení:')
            ->setDefaultValue($row['nazev'])
            ->addRule($form::MAX_LENGTH,'Název je příliš dlouhý. Maximální délka je %d.',30)
            ->setRequired('Název je povinný údaj.');

        $form->addText('datum', "Datum:")
            ->setRequired("Datum je povinný údaj")
            ->setAttribute("class", "dtpicker col-sm-2")
            ->setDefaultValue(substr($row['datum'],0,10))
            ->setAttribute('placeholder', 'rrrr-mm-dd')
            ->addRule(MyValidation::DATUM, "Datum musí být ve formátu YYYY-MM-DD");

        $form->addTextArea('popis','Popis školení:', 2,2)
            ->setDefaultValue($row['popis'])
            ->setRequired(false);

        $form->addSubmit('submit', 'Upravit školení');



        $form->onSuccess[] = [$this, 'updateTrainingSucceed'];
        return $form;
    }

    public function updateTrainingSucceed(Form $form){

        $this->trainingModel->updateTraining($form->getValues(true));
        $this->flashMessage('Školení upraveno!', 'success');
        $this->redirect('Training:show');

    }
}