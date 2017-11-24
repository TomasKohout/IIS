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
    /**
     * @persistent
     * @var int
     */
    private $id_druh_zvirete;


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
        $this->template->skoleni = $this->trainingModel->getTrainings();
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
        $values = $form->getValues(true);
        $this->template->data = $this->animalModel->searchKind($values);
    }

    public function renderUpdate($id_druh_zvirete){
        if (!$this->getUser()->isAllowed('admin')){
            $this->flashMessage('Pro přístup na tuto stránku nemáte oprávnění. Obraťte se prosím na administrátora.', 'warning');
            $this->redirect('MainPage:default');
        }


        if (!$this->animalModel->kindIsNotExist($id_druh_zvirete)) {
            $this->flashMessage('Nelze upravovat typ výběhu, který neexistuje.', 'warning');
            $this->redirect('AnimalKind:search');
        }

        $this->id_druh_zvirete = $id_druh_zvirete;
    }

    public function renderDelete($id_druh_zvirete){
        if (!$this->getUser()->isAllowed('admin')){
            $this->flashMessage('Pro přístup na tuto stránku nemáte oprávnění. Obraťte se prosím na administrátora.', 'warning');
            $this->redirect('MainPage:default');
        }


        if (!$this->animalModel->kindIsNotExist($id_druh_zvirete)) {
            $this->flashMessage('Nelze upravovat typ výběhu, který neexistuje.', 'warning');
            $this->redirect('AnimalKind:search');
        }

        try{
            $this->animalModel->deleteAnimalKind($id_druh_zvirete);
        }
        catch (Nette\Database\ForeignKeyConstraintViolationException $exception){
            $this->flashMessage('Nelze smazat typ výběhu, který už je používán!', 'warning');
            $this->redirect('CoopKind:search');
        }
        $this->redirect('AnimalKind:search');
    }

    public function createComponentUpdateAnimalKind(){
        $values = $this->animalModel->getAnimalKindValues($this->id_druh_zvirete);

        $form = $this->form();
        $form->addHidden('id_druh_zvirete')
            ->setDefaultValue($values['id_druh_zvirete']);
        $form->addSelect('naSkoleni', 'Vyber potřebné školení:', $this->trainingModel->getTrainings())
            ->setDefaultValue($values['naSkoleni'])
            ->setRequired('Školení je požadovaná hodnota!');
        $form->addText('nazev', 'Název druhu:')
            ->setDefaultValue($values['nazev'])
            ->addRule($form::MAX_LENGTH,'Název je příliš dlouhý. Maximální délka je %d.',30)
            ->setRequired('Vyplňte název druhu.');
        $form->addText('vyskyt', 'Výskyt:')
            ->setDefaultValue($values['vyskyt'])
            ->setRequired(false)
            ->addRule($form::MAX_LENGTH,'Název je příliš dlouhý. Maximální délka je %d.',30);
        $form->addSubmit('submit', 'Upravit druh');
        $form->onSuccess[] = [$this, 'updateAnimalKindSucceed'];
        return $form;
    }

    public function updateAnimalKindSucceed(Nette\Application\UI\Form $form, Nette\Utils\ArrayHash $values)
    {
        $this->animalModel->updateAnimalKind($form->getValues(true));
        $this->flashMessage('Druh upraven!' ,'success');
        $this->redirect('AnimalKind:search');
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