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

class SearchAnimalPresenter extends BasePresenter
{

    protected $database;
    public $model;


    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
        $this->model = new AnimalModel($this->database);
    }

    public function renderDefault()
    {
        $this->template->dataAll = $this->model->allAnimals();
        $this->template->druh = $this->model->getDruh();
    }

    public function createComponentSearchAnimal(){
        $form = $this->form();
        $form->addText('jmeno', 'Jméno zvířete: ')
            ->setRequired('Jmeno');

        $form->addSubmit('submit', 'Vyhledat zvíře');
        $form->onSuccess[] = [$this, 'renderAnimalSucceed'];
        return $form;
    }

    public function renderAnimalSucceed(Nette\Application\UI\Form $form){
        $this->template->data = $this->model->searchAnimal($form->getValues(true));
        //$this->template->druh = $this->model->getDruh();
        $this->template->showAnimals = true;
    }

}