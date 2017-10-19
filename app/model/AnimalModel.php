<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 19.10.17
 * Time: 11:56
 */

namespace App\Model;

use Nette;

class AnimalModel {
    protected $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }
}