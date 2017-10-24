<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 19.10.17
 * Time: 15:28
 */
namespace App\Presenters;

use App\Model\AnimalModel;
use Nette;
use Nette\Forms\Form;

class UpdateAnimalPresenter extends BasePresenter
{
    protected $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }


    public function renderUmrti()
    {

    }

    public function createComponentDeadAnimal(){
        $form = $this->form();
        $model = new AnimalModel($this->database);

        $form->addSelect('id_zvire', 'Vyber zvíře:', $model->getZvire())
            ->setPrompt('Zvol zvíře');
        $form->addText('datum_umrti', "Datum:")
            ->setRequired("Datum úmrtí je povinný údaj")
            ->setAttribute("class", "dtpicker col-sm-2")
            ->setAttribute('placeholder', 'rrrr.mm.dd')
            ->addRule($form::PATTERN, "Datum musí být ve formátu YYYY.MM.DD", "(19|20)\d\d\.(0[1-9]|1[012])\.(0[1-9]|[12][0-9]|r[01])");

        $form->addSubmit('submit', 'Upravit zvíře');

        $form->onSuccess[] = [$this, 'updateAnimalSucceed'];

        return $form;
    }

    public function createComponentUpdateAnimal(){
        $form = $this->form();
        $model = new AnimalModel($this->database);
        $sex = ['M' => 'muž', 'Z' => 'žena'];
        $form->addSelect('id_zvire', 'Vyber zvíře:', $model->getZvire())
            ->setPrompt('Zvol zvíře');
        $form->addSelect('jeDruhu', 'Druh:', $model->getDruh())
            ->setPrompt('Zvol druh');
        $form->addRadioList('pohlavi', 'Pohlaví:', $sex)
            ->setRequired();
        $form->addText('vaha', 'Váha:')
            ->setRequired();
        $form->addText('vyska', 'Výška:')
            ->setRequired();
        $form->addText('jmeno_matky', 'Jméno matky:')
            ->setRequired();
        $form->addText('jmeno_otce', 'Jméno otce:')
            ->setRequired();
        $form->addSelect('obyva', 'Výběh číslo:', $model->getTypVybehu())
            ->setPrompt('Vybeh');
        $form->addText('datum_narozeni', "Datum:")
            ->setRequired("Datum narození je povinný údaj")
            ->setAttribute("class", "dtpicker col-sm-2")
            ->setAttribute('placeholder', 'rrrr.mm.dd')
            ->addRule($form::PATTERN, "Datum musí být ve formátu YYYY.MM.DD", "(19|20)\d\d\.(0[1-9]|1[012])\.(0[1-9]|[12][0-9]|r[01])");

        $form->addSubmit('submit', 'Upravit zvíře');

        $form->onSuccess[] = [$this, 'updateAnimalSucceed'];

        return $form;
    }

    public function updateAnimalSucceed(Form $form, Nette\Utils\ArrayHash $array){
        $model = new AnimalModel($this->database);
        $model->updateAnimal($form->getValues(true));
        $this->flashMessage('Zvíře upraveno!', 'success');
        $this->redirect('UpdateAnimal:');
    }

}