<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 20.11.17
 * Time: 10:34
 */

namespace App\Presenters;
use App\Forms\MyValidation;
use App\Model\KeeperModel;
use App\Model\TasksModel;
use Nette;
use App\Model\AnimalModel;
use App\Model\FeedModel;
use Nette\Application\UI\Form;
use Nextras;

class FeedPresenter extends BasePresenter
{

    protected $database;
    protected $feedModel;
    protected $animalModel;
    protected $keeperModel;
    protected $tasksModel;
    /**
     * @persistent
     * @var int
     */
    public $id_zvire;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
        $this->feedModel = new FeedModel($this->database);
        $this->animalModel = new AnimalModel($this->database);
        $this->keeperModel = new KeeperModel($this->database);
        $this->tasksModel = new TasksModel($this->database);
    }

    protected function startup(){
        parent::startup();

        if (!$this->user->isAllowed('feed', 'view'))
        {
            $this->flashMessage('Pro přístup na tuto stránku nemáte oprávnění. Obraťte se prosím na administrátora.', 'warning');
            $this->redirect('MainPage:default');
        }
    }

    public function renderTasks($page = 1, $rd_osetrovatel, $datum){
        $paginator = new Nette\Utils\Paginator();
        $paginator->setItemsPerPage(10);
        $paginator->setPage($page);

        $tmp = $this->tasksModel->tasksFeed($rd_osetrovatel, $datum);

        $paginator->setItemCount(count($tmp));

        $wtf = array_slice($tmp, $paginator->getOffset(), $paginator->getLength());
        $this->template->dataFeed = $wtf;
        $this->template->paginator = $paginator;

    }

    public function createComponentTasks(){
        $form = $this->form();
        $form->addSelect('rd_osetrovatel', 'Ošetřovatel: ', $this->keeperModel->getRodneCisloByLogin())
            ->setPrompt("Vyber ošetřovatele");
        $form->addText('datum', "Datum:");

        $form->addSubmit('submit', 'Hledat');
        $form->onSuccess[] = [$this, 'tasksFeedSucced'];
        return $form;

    }

    public function tasksFeedSucced(Nette\Application\UI\Form $form, Nette\Utils\ArrayHash $values){
        $this->redirect('Feed:tasks', 1, $values->rd_osetrovatel, $values->datum);
    }

    public function renderFeeded($id){
        $this->tasksModel->isValid($id, 'provadi_krmeni');
        $this->tasksModel->taskFeedDone($id);
        $this->redirect('Feed:tasks');
    }


    public function renderSearch($page = 1, $id_zvire = null, $jmeno = null, $datum = null, $login = null){

        $paginator = new Nette\Utils\Paginator();
        $paginator->setItemsPerPage(10);
        $paginator->setPage($page);
        $count = '';
        if ($id_zvire != null || $jmeno != null || $datum != null || $login != null)
        {
            $array = $this->removeEmpty(['id_zvire'=> $id_zvire,'jmeno' => $jmeno, 'datum' => $datum, 'login' => $login]);

            $tmp = $this->removeEmpty(['id_zvire' => $id_zvire ,'jmeno' => $jmeno]);

            $tmp = $this->feedModel->searchFeed($array);
            $paginator->setItemCount(count($tmp));
            $wtf = array_slice($tmp, $paginator->getOffset(), $paginator->getLength());

            $this->template->data = $wtf;
            $this->template->show = true;
            $this->template->id_zvire = $id_zvire;
            $this->template->jmeno = $jmeno;
            $this->template->datum = $datum;
            $this->template->login = $login;
        }else{
            $tmp = $this->feedModel->searchFeed();
            $paginator->setItemCount(count($tmp));

            $wtf = $wtf = array_slice($tmp, $paginator->getOffset(), $paginator->getLength());
            $this->template->dataAll = $wtf;
        }
        $this->template->paginator = $paginator;
        $this->template->druh = $this->animalModel->getZvire();
    }

    public function createComponentSearchFeed(){
        $form = $this->form();
        $form->addText('id_zvire', 'ID zvířete: ');
        $form->addText('jmeno', 'Jméno zvířete: ');
        $form->addText('datum', 'Datum: ');
        $form->addText('login', 'Ošetřovatel: ');


        $form->addSubmit('submit', 'Vyhledat krmení');
        $form->onSuccess[] = [$this, 'searchFeedSucceed'];
        return $form;
    }

    public function searchFeedSucceed(Nette\Application\UI\Form $form, Nette\Utils\ArrayHash $values){
        $this->redirect('Feed:search', 1, $values->id_zvire, $values->jmeno, $values->datum, $values->login);
    }

    public function renderAdd($id_zvire){
        if (!$this->user->isAllowed('feed', 'add'))
        {
            $this->flashMessage('Pro přístup na tuto stránku nemáte oprávnění. Obraťte se prosím na administrátora.', 'warning');
            $this->redirect('MainPage:default');
        }

        if ($this->animalModel->isDead($id_zvire)) {
            $this->flashMessage('Nelze upravovat zvířata, která jsou vedena jako mrtvá.', 'warning');
            $this->redirect('Animal:search');
        }
        $this->animalModel->isValidId($id_zvire);
        $this->id_zvire = $id_zvire;
    }

    public function createComponentAddFeed()
    {
        $form = $this->form();
        $form->addHidden('jeKrmeno', 'ID zvířete: ')
            ->setDefaultValue($this->id_zvire);
        $form->addText('datum', "Datum:")
            ->setDefaultValue(StrFTime("%Y-%m-%d", Time()))
            ->setRequired("Datum a čas krmení je povinný údaj")
            ->setAttribute("class", "dtpicker col-sm-2")
            ->setAttribute('placeholder', 'YYYY-MM-DD')
            ->addRule(MyValidation::DATUM, "Datum musí být ve formátu YYYY-MM-DD");
        $form->addText('druh', 'Krmivo:')
            ->setRequired('Krmivo je povinný údaj.');
        $form->addText('mnozstvi', 'Množství:');
        $form->addSelect('rd_osetrovatel', 'Ošetřovatel:', $this->feedModel->getRodneCisloByLoginWithTraining($this->id_zvire))
            ->setPrompt("Zvolte ošetřovatele")
            ->setRequired("Ošetřovatel je povinný údaj.");


        $form->addSubmit('submit', 'Přidat');
        $form->onSuccess[] = [$this, 'addFeedSucceed'];
        return $form;

    }

    public function addFeedSucceed(Form $form, Nette\Utils\ArrayHash $values)
    {
        $this->feedModel->addFeed($form->getValues(true));
        $this->flashMessage('Krmení přidáno!' ,'success');
        $this->redirect('Animal:search');

    }

}