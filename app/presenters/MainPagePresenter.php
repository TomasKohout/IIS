<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 18.10.17
 * Time: 21:50
 */

namespace App\Presenters;
use App\Model\KeeperModel;
use App\Model\TasksModel;
use Nette;

class MainPagePresenter extends BasePresenter
{
    protected $database;
    protected $keeperModel;
    protected $tasksModel;

    public function __construct(Nette\Database\Context $database)
    {

        $this->tasksModel  = new TasksModel($database);
        $this->keeperModel = new KeeperModel($database);
        $this->database = $database;
    }

    public function renderDefault(){
        if ($this->keeperModel->checkForPass($this->user->getId()))
            $this->flashMessage("Změňte si heslo!", "danger");

        $this->template->dataClean = $this->tasksModel->tasksClean($this->user->getId(), "");
        $this->template->dataFeed  = $this->tasksModel->tasksFeed($this->user->getId(), "");
    }

    public function renderCleaned($id){

        $this->tasksModel->isValid($id, 'provadi_cisteni');
        $this->tasksModel->taskCleanDone($id);
        $this->redirect('MainPage:default');
    }


    public function renderFeeded($id){
        $this->tasksModel->isValid($id, 'provadi_krmeni');
        $this->tasksModel->taskFeedDone($id);
        $this->redirect('MainPage:default');
    }
}