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
        if (!$this->user->isAllowed('kind', 'view')){
            $this->flashMessage('Pro přístup do této stránky nemáte oprávnění. Obraťte se na administrátora.', 'warning');
            $this->redirect('MainPage:default');
        }
        $this->template->user = $this->getUser();
    }

    public function renderAdd()
    {
        if (!$this->user->isAllowed('addKind', 'add')){
            $this->flashMessage('Pro přístup do této stránky nemáte oprávnění. Obraťte se na administrátora.', 'warning');
            $this->redirect('MainPage:default');
        }
    }

    public function renderSearch($page = 1, $id_druh_zvirete = null, $nazev = null){
        $paginator = new Nette\Utils\Paginator();
        $paginator->setItemsPerPage(10);
        $paginator->setPage($page);
        if ($id_druh_zvirete != null || $nazev != null)
        {
            $tmp = $this->removeEmpty(['nazev' => $nazev,'id_druh_zvirete' => $id_druh_zvirete]);
            $kindCount = $this->animalModel->getKindCount(array($tmp));
            $paginator->setItemCount($kindCount);
            $this->template->data = $this->animalModel->searchKind($paginator->getLength(), $paginator->getOffset(), $tmp);

            $this->template->nazev = $nazev;
            $this->template->id_druh_zvirete = $id_druh_zvirete;
        }
        else
        {
            $kindCount = $this->animalModel->getKindCount();
            $paginator->setItemCount($kindCount);
            $this->template->dataAll = $this->animalModel->searchKind($paginator->getLength(), $paginator->getOffset(),[]);
        }
        $this->template->skoleni = $this->trainingModel->getTrainings();
        $this->template->paginator = $paginator;

    }

    public function createComponentSearch(){
        $form = $this->form();
        $form->addText('id_druh_zvirete', 'ID druhu:')
            ->setRequired(false)
            ->setHtmlType('number');
        $form->addSelect('nazev', 'Název druhu:', $this->animalModel->getAllKind())
            ->setPrompt('Vyber druh')
            ->setRequired(false);

        $form->addSubmit('submit', 'Hledat');

        $form->onSuccess[] = [$this, 'searchSucceed'];
        return $form;
    }

    public function searchSucceed(Form $form, Nette\Utils\ArrayHash $val){
        $this->redirect('AnimalKind:search',1, $val->id_druh_zvirete, $val->nazev);
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