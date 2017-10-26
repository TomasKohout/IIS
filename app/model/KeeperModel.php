<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 26.10.17
 * Time: 13:55
 */

namespace App\Model;

use Nette;

class KeeperModel
{
    protected $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function addKeeper(array $values)
    {

        $this->database->table('osetrovatel')->insert($values);
    }

}