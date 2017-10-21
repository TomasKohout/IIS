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

class UpdateAnimalPresenter extends \Nette\Application\UI\Presenter
{

    /** @var \Instante\ExtendedFormMacros\IFormFactory @inject */
    public $formFactory;
    protected $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }


    public function createComponentUpdateAnimal(){
        $form = $this->formFactory->create();
        $form->addText('id_zvire' , 'Idetifikační číslo zvirete:')
            ->addRule($form::PATTERN, 'Musí být číslo', '[0-9]*')
            ->setRequired();
        $form->addText('jmeno', 'Upravit jméno:');
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