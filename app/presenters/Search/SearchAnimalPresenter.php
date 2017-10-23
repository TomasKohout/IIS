<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 21.10.17
 * Time: 20:19
 */

namespace App\Presenters;
use App\Model\AnimalModel;
use Nette;
use Nette\Application\UI\Form;

class SearchAnimalPresenter extends \Nette\Application\UI\Presenter
{

    protected $database;
    public $result;


    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function renderDefault()
    {
    }

    public function createComponentSearchAnimal(){
        $form = new Form;
        $form->addText('jmeno', 'Jméno zvířete: ')
            ->setRequired();

        $form->addSubmit('submit', 'Vyhledat zvíře');
        $form->onSuccess[] = [$this, 'renderAnimalSucceed'];
        return $form;
    }

    public function renderAnimalSucceed(Form $form){
        $model = new AnimalModel($this->database);
        $this->template->data = $model->searchAnimal($form->getValues(true));
        $this->template->showAnimals = true;
    }

}