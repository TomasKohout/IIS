<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 20.11.17
 * Time: 10:34
 */

namespace App\Presenters;
use Nette;
use App\Model\AnimalModel;
use App\Model\FeedModel;
use Nette\Application\UI\Form;
use Nextras;

class FeedPresenter extends BasePresenter
{

    protected $database;
    protected $model;
    protected $animalModel;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
        $this->model = new FeedModel($this->database);
        $this->animalModel = new AnimalModel($this->database);
    }

    protected function startup(){
        parent::startup();

        if (!$this->user->isAllowed('feed', 'add'))
        {
            $this->flashMessage('Pro přístup na tuto stránku nemáte oprávnění. Obraťte se prosím na administrátora.', 'warning');
            $this->redirect('MainPage:default');
        }
    }


    public function renderSearch(){
        $this->template->dataAll = $this->model->allFeed();
        $this->template->druh = $this->animalModel->getZvire();
    }

    public function renderAdd(){

    }

    public function createComponentSearchFeed(){

        $form = $this->form();
        $form->addText('jeKrmeno', 'ID zvířete: ');

        $form->addSubmit('submit', 'Vyhledat krmení');
        $form->onSuccess[] = [$this, 'renderSearchFeedlSucceed'];
        return $form;
    }

    public function renderSearchFeedlSucceed(Nette\Application\UI\Form $form){
        $this->template->data = $this->model->searchFeed($form->getValues(true));
        $this->template->show = true;
    }

    public function createComponentAddFeed()
    {

        $form = $this->form();
        $form->addText('jeKrmeno', 'ID zvířete: ')
            ->setRequired('Jméno je povinný údaj.');
        $form->addHidden('cas', "Datum:")
            ->setDefaultValue(StrFTime("%Y.%m.%d", Time()))
            ->setRequired("Datum a čas krmení je povinný údaj")
            ->setAttribute("class", "dtpicker col-sm-2")
            ->setAttribute('placeholder', 'rrrr.mm.dd')
            ->addRule($form::PATTERN, "Datum musí být ve formátu YYYY.MM.DD", "(19|20)\d\d\.(0[1-9]|1[012])\.(0[1-9]|[12][0-9]|r[01])");
        $form->addText('druh', 'Krmivo:')
            ->setRequired('Krmivo je povinný údaj.');
        $form->addText('mnozstvi', 'Množství:');

        $form->addSubmit('submit', 'Přidat');
        $form->onSuccess[] = [$this, 'addFeedSucceed'];
        return $form;

    }

    public function addFeedSucceed(Form $form, Nette\Utils\ArrayHash $values)
    {
        $this->model->addFeed($form->getValues(true));
        $this->flashMessage('Krmení přidáno!' ,'success');
        $this->redirect('Feed:add');

    }

}