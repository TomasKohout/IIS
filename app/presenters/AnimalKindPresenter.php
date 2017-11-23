<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 20.11.17
 * Time: 17:20
 */

namespace App\Presenters;
use App\Model\TrainingModel;
use Nette;
use App\Model\AnimalModel;
use Nette\Application\UI\Form;

class AnimalKindPresenter extends BasePresenter
{

    protected $database;
    protected $trainingModel;
    protected $animalModel;


    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
        $this->animalModel = new AnimalModel($database);
        $this->trainingModel    = new TrainingModel($database);

    }

    protected function startup(){
        parent::startup();
        if (!$this->user->isAllowed('addKind', 'add')){
            $this->flashMessage('Pro přístup do této stránky nemáte oprávnění. Obraťte se na administrátora.', 'warning');
            $this->redirect('MainPage:default');
        }
        $this->template->user = $this->getUser();
    }

    public function renderAdd()
    {

    }

    public function renderSearch(){
        $this->template->dataAll = $this->animalModel->searchKind([]);
    }

    public function createComponentSearch(){
        $form = $this->form();
        $form->addText('id_druh_zvirete', 'ID druhu:')
            ->setRequired(false)
            ->setHtmlType('number');
        $form->addText('nazev', 'Název druhu:')
            ->setRequired(false)
            ->addRule(Nette\Forms\Form::MAX_LENGTH, 'Maximální délka je 30 znaků.', 30);

        $form->addSubmit('submit', 'Hledat');

        $form->onSuccess[] = [$this, 'searchSucceed'];
        return $form;
    }

    public function searchSucceed(Form $form){
        $this->template->data = $this->animalModel->searchKind($form->getValues(true));

    }

    public function renderUpdate(){

    }

    public function renderDelete(){

    }

    public function createComponentAddDruhZvirete(){
        $form = $this->form();
        $form->addSelect('naSkoleni', 'Vyber potřebné školení:', $this->trainingModel->getTrainings())
            ->setRequired('Školení je požadovaná hodnota!');
        $form->addText('nazev', 'Název druhu:')
            ->setDefaultValue('Název')
            ->addRule($form::MAX_LENGTH,'Název je příliš dlouhý. Maximální délka je %d.',30)
            ->setRequired('Vyplňte název druhu.');
        $form->addText('vyskyt', 'Výskyt:')
            ->setRequired(false)
            ->addRule($form::MAX_LENGTH,'Název je příliš dlouhý. Maximální délka je %d.',30);
        $form->addSubmit('submit', 'Přidat druh');
        $form->onSuccess[] = [$this, 'addDruhZvireteSucceed'];
        return $form;
    }

    public function addDruhZvireteSucceed(Nette\Application\UI\Form $form, Nette\Utils\ArrayHash $values)
    {
        $this->animalModel->addDruh($form->getValues(true));
        $this->flashMessage('Druh přidán!' ,'success');
        $this->redirect('AnimalKind:add');
    }
}