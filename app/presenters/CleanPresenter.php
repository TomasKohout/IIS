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
use App\Model\CleanModel;
use Nette\Application\UI\Form;
use Nextras;
use App\Forms\MyValidation;
use Nette\Forms\Container;


class CleanPresenter extends BasePresenter
{

    protected $database;
    protected $cleanModel;
    protected $coopModel;
    /**
     * @persistent
     * @var int;
     */
    public $id_vybeh;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
        $this->cleanModel = new CleanModel($this->database);
        $this->coopModel = new CoopModel($this->database);
    }

    protected function startup(){
        parent::startup();

        if (!$this->user->isAllowed('clean', 'view'))
        {
            $this->flashMessage('Pro přístup na tuto stránku nemáte oprávnění. Obraťte se prosím na administrátora.', 'warning');
            $this->redirect('MainPage:default');
        }
    }


    public function renderSearch($page = 1, $id_vybeh = null, $datum = null, $login = null){

        $paginator = new Nette\Utils\Paginator();
        $paginator->setItemsPerPage(10);
        $paginator->setPage($page);
        $count = 0;
        if ($id_vybeh != null || $datum != null || $login  != null){
            $tmp = $this->removeEmpty(['id_vybeh' => $id_vybeh, 'datum' => $datum, 'login' => $login]);
            $tmp = $this->cleanModel->searchClean($tmp);

            $paginator->setItemCount(count($tmp));

            $wtf = array_slice($tmp, $paginator->getOffset(), $paginator->getLength());

            $this->template->data = $wtf;
            $this->template->show = true;
            $this->template->id_vybeh = $id_vybeh;
            $this->template->datum = $datum;
            $this->template->login = $login;
        }
        else
        {
            $tmp = $this->cleanModel->searchClean();

            $paginator->setItemCount(count($tmp));

            $wtf = array_slice($tmp, $paginator->getOffset(), $paginator->getLength());

            $paginator->setItemCount(count($tmp));
            $this->template->dataAll = $wtf;
        }

        $this->template->paginator = $paginator;

    }

    public function createComponentSearchClean(){

        $form = $this->form();
        $form->addText('id_vybeh', 'ID výběhu: ');
        $form->addText('datum', 'Datum: ');
        $form->addText('login', 'Ošetřovatel: ');


        $form->addSubmit('submit', 'Vyhledat krmení');
        $form->onSuccess[] = [$this, 'searchCleanSucceed'];
        return $form;
    }

    public function searchCleanSucceed(Nette\Application\UI\Form $form, Nette\Utils\ArrayHash $values){
        $this->redirect('Clean:search', 1, $values->id_vybeh, $values->datum, $values->login);
    }

    public function renderAdd($id_vybeh){
        if (!$this->user->isAllowed('clean', 'add'))
        {
            $this->flashMessage('Pro přístup na tuto stránku nemáte oprávnění. Obraťte se prosím na administrátora.', 'warning');
            $this->redirect('MainPage:default');
        }

        $this->coopModel->isValidId($id_vybeh);
        $this->id_vybeh = $id_vybeh;
    }

    public function createComponentAddClean()
    {
        $form = $this->form();
        $form->addHidden('jeCisten', 'ID výběhu: ')
            ->setDefaultValue($this->id_vybeh);
        $form->addText('datum', "Datum:")
            ->setDefaultValue(StrFTime("%Y-%m-%d", Time()))
            ->setRequired("Datum a čas krmení je povinný údaj")
            ->setAttribute("class", "dtpicker col-sm-2")
            ->setAttribute('placeholder', 'YYYY-MM-DD')
            ->addRule(MyValidation::DATUM, "Datum musí být ve formátu YYYY-MM-DD");

        $form->addDynamic('osetrovatele', function ( Container $container){
                $container->addSelect('rd_osetrovatel', 'Ošetřovatel:', $this->cleanModel->getRodneCisloByLoginWithTraining($this->id_vybeh))
                    ->setPrompt("Zvolte ošetřovatele")
                    ->setRequired("Ošetřovatel je povinný údaj.");
        },intval($this->cleanModel->getNumberOfNeededKeepersToClean($this->id_vybeh)));

        $form->addSubmit('submit', 'Přidat');
        $form->onSuccess[] = [$this, 'addCleanSucceed'];
        return $form;

    }

    public function addCleanSucceed(Form $form, Nette\Utils\ArrayHash $values)
    {
        if($this->arrayHasDupes($form['osetrovatele']->getValues(true))){
            $form->addError("Je nutno zadat různé ošetřovatele");
        }else{
            $this->cleanModel->addClean($form->getValues(true));
            $this->flashMessage('Čištění přidáno!' ,'success');
            $this->redirect('Coop:search');
        }
    }

}