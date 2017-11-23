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
    }

    protected function startup(){
        parent::startup();

        if (!$this->user->isAllowed('feed', 'view'))
        {
            $this->flashMessage('Pro přístup na tuto stránku nemáte oprávnění. Obraťte se prosím na administrátora.', 'warning');
            $this->redirect('MainPage:default');
        }
    }


    public function renderSearch(){
        $arr = array();
        $this->template->dataAll = $this->feedModel->searchFeed($arr);
        $this->template->druh = $this->animalModel->getZvire();
    }

    public function renderAdd($id_zvire){
        if (!$this->user->isAllowed('feed', 'add'))
        {
            $this->flashMessage('Pro přístup na tuto stránku nemáte oprávnění. Obraťte se prosím na administrátora.', 'warning');
            $this->redirect('MainPage:default');
        }

        $this->animalModel->isValidId($id_zvire);
        $this->id_zvire = $id_zvire;
    }


    public function createComponentSearchFeed(){
        $form = $this->form();
        $form->addText('id_zvire', 'ID zvířete: ');
        $form->addText('jmeno', 'Jméno zvířete: ');
        $form->addText('datum', 'Datum: ');


        $form->addSubmit('submit', 'Vyhledat krmení');
        $form->onSuccess[] = [$this, 'searchFeedSucceed'];
        return $form;
    }

    public function searchFeedSucceed(Nette\Application\UI\Form $form){
        $this->template->data = $this->feedModel->searchFeed($form->getValues(true));
        if(!empty($this->template->data)){
            $this->template->show = true;
        }
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