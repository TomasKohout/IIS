<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 20.11.17
 * Time: 10:34
 */

namespace App\Presenters;
use App\Model\CoopModel;
use Nette;
use Nette\Application\UI\Form;

class CoopPresenter extends BasePresenter
{

    protected $database;
    protected $model;
    protected $id_vybeh;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
        $this->model = new CoopModel($database);
    }
    public function renderSearch(){

    }

    public function renderAdd(){

    }

    public function renderShow(){
        $model = new CoopModel($this->database);
        $this->template->data = $model->showCoop();
    }

    public function renderDefault(){

    }

    public function renderUpdate($id_vybeh){
        $this->id_vybeh = $id_vybeh;
    }

    public function createComponentUpdateCoop(){
        $values = $this->model->getCoopCalues($this->id_vybeh);

        $form = $this->form();
        $form->addSelect('naTypVybehu','Velikost výběhu: ' , $this->model->getTypeOfCoop())
            ->setDefaultValue($values['naTypVybehu']);
        $form->addText('poloha', 'Poloha výběhu: ')
            ->setDefaultValue($values['poloha'])
            ->setRequired('Poloha');
        $form->addText('rozloha', 'Rozloha: ')
            ->setDefaultValue($values['rozloha'])
            ->setHtmlType('number')
            ->setRequired('Zadej rozlohu.');
        $form->addTextArea('popis', 'Popis: ')
            ->setDefaultValue($values['popis'])
            ->setRequired(false)
            ->addRule(Form::MAX_LENGTH, "Maximální délka popisu je 255 znaků!", 255);

        $form->addSubmit('submit', 'Upravit výběh');

        $form->onSuccess[] = [$this, 'updateCoopSucceed'];
        return $form;


    }

    public function updateCoopSucceed(Form $form){
        $this->model->updateCoop($form->getValues(true));
        $this->flashMessage('Výběh upraven!', 'success');
        $this->redirect('Coop:search');
    }


    public function createComponentSearchCoop(){
        $form = $this->form();
        $form->addText('nazev', 'Název výběhu: ')
            ->setRequired('Nazev');

        $form->addSubmit('submit', 'Vyhledat výběh');
        $form->onSuccess[] = [$this, 'SearchCoopSucceed'];
        return $form;
    }

    public function SearchCoopSucceed(Form $form){

        $this->template->data = $this->model->searchCoop($form->getValues(true));
        $this->template->show = true;
    }

    public function createComponentAddCoop(){
        $form = $this->form();
        $form->addSelect('naTypVybehu','Velikost výběhu: ' , $this->model->getTypeOfCoop())
            ->setPrompt('Vyber velikost');
        $form->addText('poloha', 'Poloha výběhu: ')
            ->setRequired('Poloha');
        $form->addText('rozloha', 'Rozloha: ')
            ->setHtmlType('number')
            ->setRequired('Zadej rozlohu.');
        $form->addTextArea('popis', 'Popis: ')
            ->setRequired(false)
            ->addRule(Form::MAX_LENGTH, "Maximální délka popisu je 255 znaků!", 255);

        $form->addSubmit('submit', 'Přidat výběh');

        $form->onSuccess[] = [$this, 'addCoopSucceed'];
        return $form;

    }

    public function addCoopSucceed(Form $form){
        $this->model->addCoop($form->getValues(true));
        $this->flashMessage('Výběh přidán!' ,'success');

        $this->redirect('Coop:add');
    }
}