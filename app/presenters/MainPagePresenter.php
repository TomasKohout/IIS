<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 18.10.17
 * Time: 21:50
 */

namespace App\Presenters;
use App\Model\KeeperModel;
use Nette;

class MainPagePresenter extends BasePresenter
{
    protected $database;
    protected $keeperModel;

    public function __construct(Nette\Database\Context $database)
    {

        $this->keeperModel = new KeeperModel($database);
        $this->database = $database;
    }

    public function renderDefault(){
        if ($this->keeperModel->checkForPass($this->user->getId()))
            $this->flashMessage("Změňte si heslo!", "danger");

        $this->template->data = $this->database->query('SELECT zvire.id_zvire,zvire.jmeno, krmeni.datum FROM zvire LEFT JOIN krmeni ON zvire.id_zvire = krmeni.jekrmeno ORDER BY zvire.id_zvire DESC');
    }
}