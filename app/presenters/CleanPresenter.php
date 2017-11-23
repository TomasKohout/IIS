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


    public function renderSearch(){
        $arr = array();
        $this->template->dataAll = $this->cleanModel->searchClean($arr);
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

    public function createComponentSearchClean(){

        $form = $this->form();
        $form->addText('jeCisten', 'ID výběhu: ');

        $form->addSubmit('submit', 'Vyhledat krmení');
        $form->onSuccess[] = [$this, 'searchCleanSucceed'];
        return $form;
    }

    public function searchCleanSucceed(Nette\Application\UI\Form $form){
        $this->template->data = $this->cleanModel->searchClean($form->getValues(true));
        $this->cleanModel->getCleaners("1");
        $this->template->show = true;
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