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

    protected function startup(){
        parent::startup();


    }
    public function renderSearch($page = 1, $id_vybehu = null, $poloha = null){
        if (!$this->user->isAllowed('coop', 'view'))
        {
            $this->flashMessage('Pro přístup na tuto stránku nemáte oprávnění. Obraťte se prosím na administrátora.', 'warning');
            $this->redirect('MainPage:default');
        }
        $paginator = new Nette\Utils\Paginator();
        $paginator->setItemsPerPage(10);
        $paginator->setPage($page);
        if ($id_vybehu != null || $poloha != null){
            $value = $this->removeEmpty(['id_vybehu'=> $id_vybehu, 'poloha' => $poloha]);
            $coopCount = $this->model->getCountOfCoops($value);
            $paginator->setItemCount($coopCount);
            $this->template->data = $this->model->searchCoop($paginator->getLength(), $paginator->getOffset(), $value);
            $this->template->show = true;
            $this->template->id_vybehu = $id_vybehu;
            $this->template->poloha = $poloha;
        }
        else
        {
            $coopCount = $this->model->getCountOfCoops();
            $paginator->setItemCount($coopCount);
            $this->template->dataAll = $this->model->searchCoop($paginator->getLength(), $paginator->getOffset());
        }

        $this->template->paginator = $paginator;

    }

    public function createComponentSearchCoop(){
        $form = $this->form();
        $form->addText('id_vybeh', 'ID výběhu: ');
        $form->addText('poloha', 'Poloha výběhu: ');

        $form->addSubmit('submit', 'Vyhledat výběh');
        $form->onSuccess[] = [$this, 'SearchCoopSucceed'];
        return $form;
    }

    public function SearchCoopSucceed(Form $form,Nette\Utils\ArrayHash $values){
        $this->redirect('Coop:search', 1, $values->id_vybeh, $values->poloha);
    }

    public function renderAdd(){
        if (!$this->user->isAllowed('coop', 'add'))
        {
            $this->flashMessage('Pro přístup na tuto stránku nemáte oprávnění. Obraťte se prosím na administrátora.', 'warning');
            $this->redirect('MainPage:default');
        }
    }


    public function renderUpdate($id_vybeh){
        if (!$this->user->isAllowed('coop', 'add'))
        {
            $this->flashMessage('Pro přístup na tuto stránku nemáte oprávnění. Obraťte se prosím na administrátora.', 'warning');
            $this->redirect('MainPage:default');
        }
        $this->model->isValidID($id_vybeh);
        $this->id_vybeh = $id_vybeh;
    }

    public function createComponentUpdateCoop(){
        $values = $this->model->getCoopValues($this->id_vybeh);

        $form = $this->form();
        $form->addHidden('id_vybeh')
            ->setDefaultValue($values['id_vybeh']);
        $form->addSelect('naTypVybehu','Velikost výběhu: ' , $this->model->getTypeOfCoop())
            ->setDefaultValue($values['naTypVybehu'])
            ->setRequired('Velikost je povinný údaj.');
        $form->addText('poloha', 'Poloha výběhu: ')
            ->setDefaultValue($values['poloha'])
            ->setRequired('Poloha je povinný údaj.');
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

    public function createComponentAddCoop(){
        $form = $this->form();
        $form->addSelect('naTypVybehu','Typ výběhu: ' , $this->model->getTypeOfCoop())
            ->setPrompt('Vyber typ')
            ->setRequired('Velikost je povinný údaj.');
        $form->addText('poloha', 'Poloha výběhu: ')
            ->setRequired('Poloha je povinný údaj.');
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