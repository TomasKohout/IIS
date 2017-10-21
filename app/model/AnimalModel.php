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

    public function addAnimal(array $values)
    {
        $this->database->table('zvire')->insert($values);
    }

    public function updateAnimal(array $values){
        $this->database->table('zvire')->where('id_zvire', $values['id_zvire'])
            ->update($values);
    }

}