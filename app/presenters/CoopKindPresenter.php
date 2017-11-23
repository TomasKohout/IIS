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
use App\Model\CoopModel;

class CoopKindPresenter extends BasePresenter
{

    protected $database;
    protected $trainingModel;
    protected $coopModel;
    /**
     * @persistent
     * @var int
     */
    private $id_typ_vybehu;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
        $this->trainingModel    = new TrainingModel($database);
        $this->coopModel    = new CoopModel($database);

    }

    protected function startup(){
        parent::startup();
        if (!$this->user->isAllowed('addKind', 'add')){
            $this->flashMessage('Pro přístup do této stránky nemáte oprávnění. Obraťte se na administrátora.', 'warning');
            $this->redirect('MainPage:default');
        }
    }

    public function renderAdd()
    {

    }

    public function renderDelete($id_typ_vybehu)
    {
        if (!$this->getUser()->isAllowed('animal', 'add')){
            $this->flashMessage('Pro přístup na tuto stránku nemáte oprávnění. Obraťte se prosím na administrátora.', 'warning');
            $this->redirect('MainPage:default');
        }


        if (!$this->coopModel->isNotExist($id_typ_vybehu)) {
            $this->flashMessage('Nelze upravovat typ výběhu, který neexistuje.', 'warning');
            $this->redirect('CoopKind:search');
        }

        try{
            $this->coopModel->deleteCoopKind($id_typ_vybehu);
        }
        catch (Nette\Database\ForeignKeyConstraintViolationException $exception){
            $this->flashMessage('Nelze smazat typ výběhu, který už je používán!', 'warning');
            $this->redirect('CoopKind:search');
        }
        $this->redirect('CoopKind:search');
    }

    public function renderUpdate($id_typ_vybehu)
    {
        if (!$this->getUser()->isAllowed('animal', 'add')){
            $this->flashMessage('Pro přístup na tuto stránku nemáte oprávnění. Obraťte se prosím na administrátora.', 'warning');
            $this->redirect('MainPage:default');
        }


        if (!$this->coopModel->isNotExist($id_typ_vybehu)) {
            $this->flashMessage('Nelze upravovat typ výběhu, který neexistuje.', 'warning');
            $this->redirect('CoopKind:search');
        }

        $this->id_typ_vybehu = $id_typ_vybehu;
    }

    public function createComponentUpdateCoopKind(){

        $values = $this->coopModel->getCoopKindValues(['id_typ_vybehu' => $this->id_typ_vybehu]);


        $form = $this->form();
        $form->addHidden('id_typ_vybehu')
            ->setDefaultValue($values['id_typ_vybehu']);
        $form->addSelect('naSkoleni', 'Vyber potřebné školení:', $this->trainingModel->getTrainings())
            ->setDefaultValue($values['naSkoleni'])
            ->setRequired('Školení je požadovaná hodnota!');
        $form->addText('nazev', 'Název:')
            ->setDefaultValue($values['nazev']);
        $form->addText('pocet_osetrovatelu', 'Potřebný počet ošetřovatelů k čištění')
            ->setDefaultValue($values['pocet_osetrovatelu'])
            ->setRequired("Potřebný počet ošetřovatelů k čištění je povinný údaj")
            ->setHtmlType('number');
        $form->addText('pomucka_k_cisteni', 'Pomůcka k čištění:')
            ->setDefaultValue($values['pomucka_k_cisteni']);
        $form->addText('doba_cisteni', 'Přepokládaná doba čištění (minut):')
            ->setDefaultValue($values['doba_cisteni'])
            ->setHtmlType('number');

        $form->addSubmit('submit', 'Upravit druh');
        $form->onSuccess[] = [$this, 'updateTypVybehuSucceed'];
        return $form;
    }

    public function updateTypVybehuSucceed(Nette\Application\UI\Form $form, Nette\Utils\ArrayHash $values)
    {
        $this->coopModel->updateCoopKind($form->getValues(true));
        $this->flashMessage('Druh upraven!' ,'success');
        $this->redirect('CoopKind:search');
    }


    public function renderSearch()
    {
        $arr = array();
        $this->template->dataAll = $this->coopModel->searchCoopKind($arr);
    }

    public function createComponentSearchCoopKind(){
        $form = $this->form();

        $form->addText('id_typ_vybehu', 'ID Typ výběhu:');
        $form->addText('nazev', 'Název:');


        $form->addSubmit('submit', 'Hledat');
        $form->onSuccess[] = [$this, 'searchSearchCoopKindSucceed'];
        return $form;
    }

    public function searchSearchCoopKindSucceed(Nette\Application\UI\Form $form, Nette\Utils\ArrayHash $values)
    {
        $this->template->data = $this->coopModel->searchCoopKind($form->getValues(true));
        $this->template->show = true;
    }


    public function createComponentAddTypVybehu(){
        $form = $this->form();
        $form->addSelect('naSkoleni', 'Vyber potřebné školení:', $this->trainingModel->getTrainings())
            ->setRequired('Školení je požadovaná hodnota!');
        $form->addText('nazev', 'Název:');
        $form->addText('pocet_osetrovatelu', 'Potřebný počet ošetřovatelů k čištění')
            ->setRequired("Potřebný počet ošetřovatelů k čištění je povinný údaj")
            ->setHtmlType('number');
        $form->addText('pomucka_k_cisteni', 'Pomůcka k čištění:');
        $form->addText('doba_cisteni', 'Přepokládaná doba čištění (minut):')
            ->setHtmlType('number');

        $form->addSubmit('submit', 'Přidat druh');
        $form->onSuccess[] = [$this, 'addTypVybehuSucceed'];
        return $form;
    }

    public function addTypVybehuSucceed(Nette\Application\UI\Form $form, Nette\Utils\ArrayHash $values)
    {
        $this->coopModel->addCoopKind($form->getValues(true));
        $this->flashMessage('Druh přidán!' ,'success');
        $this->redirect('CoopKind:search');
    }
}