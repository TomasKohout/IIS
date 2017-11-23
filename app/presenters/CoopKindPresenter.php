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
        $this->coopModel->addTyp($form->getValues(true));
        $this->flashMessage('Druh přidán!' ,'success');
        $this->redirect('CoopKind:add');
    }
}