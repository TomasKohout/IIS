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
    /**
     * @persistent
     * @var
     */
    public $id_skoleni;
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
        $this->id_skoleni = $id_skoleni;

        try{
            $this->trainingModel->deleteTraining($id_skoleni);
        }
        catch (Nette\Database\ForeignKeyConstraintViolationException $exception){
            $this->flashMessage('Nelze smazat školení, které již někdo má!', 'warning');
            $this->redirect('Training:search');
        }
        $this->flashMessage('Školení smazáno!'. $id_skoleni, 'success');
        $this->redirect('Training:search');

    }

    public function renderUpdate($id_skoleni){
        if (!$this->user->isAllowed('admin'))
        {
            $this->flashMessage('Pro přístup na tuto stránku nemáte oprávnění. Obraťte se prosím na administrátora.', 'warning');
            $this->redirect('MainPage:default');
        }
        $this->id_skoleni = $id_skoleni;

        $this->trainingModel->isValidID($this->id_skoleni);

    }


    public function renderSearch($page = 1, $nazev = null){
        if (!$this->user->isAllowed('training', 'view'))
        {
            $this->flashMessage('Pro přístup na tuto stránku nemáte oprávnění. Obraťte se prosím na administrátora.', 'warning');
            $this->redirect('MainPage:default');
        }

        $paginator = new Nette\Utils\Paginator();
        $paginator->setItemsPerPage(2);
        $paginator->setPage($page);
        if ($nazev != null){
            $values = ['nazev' => $nazev];
            $count = $this->trainingModel->getCountOfTrainings($values);
            $paginator->setItemCount($count);
            $this->template->data = $this->trainingModel->searchTrainings($paginator->getLength(), $paginator->getOffset(), $values);

            $this->template->show = true;
            $this->template->nazev = $nazev;
        }
        else{

            $count = $this->trainingModel->getCountOfTrainings();
            $paginator->setItemCount($count);
            $this->template->dataAll = $this->trainingModel->searchTrainings($paginator->getLength(), $paginator->getOffset());
        }

        $this->template->paginator = $paginator;


    }

    public function createComponentSearchTraining(){
        $form = $this->form();
        $form->addText("nazev", "Název:");

        $form->addSubmit('submit', "Hledat");
        $form->onSuccess[] = [$this, 'searchTrainingSucceed'];

        return $form;
    }

    public function searchTrainingSucceed(Form $form, Nette\Utils\ArrayHash $values){
        $this->redirect('Training:search',1, $values->nazev);
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
        $form->addSelect('id_skoleni', 'Školení: ', $this->trainingModel->getAllTrainingsSelect());
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

    public function createComponentAddSkoleni(){
        $form = $this->form();

        $form->addText('nazev', 'Název školení:')
            ->setDefaultValue('Název')
            ->addRule($form::MAX_LENGTH,'Název je příliš dlouhý. Maximální délka je %d.',30)
            ->setRequired('Název je povinný údaj.');

        $form->addText('datum', "Datum:")
            ->setDefaultValue(StrFTime("%Y-%m-%d", Time()))
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
        $this->trainingModel->addTraining($form->getValues(true));

        $this->flashMessage('Školení přidáno!' ,'success');
        $this->redirect('Training:search');


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
        $this->redirect('Training:search');

    }
}