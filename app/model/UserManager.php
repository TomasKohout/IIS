<?php

namespace App\Model;

use Nette;
use Nette\Security\Passwords;
use Nette\Database\Table\Selection;

/**
 * Users management.
 */
class UserManager implements Nette\Security\IAuthenticator
{
	use Nette\SmartObject;

	const
		TABLE_NAME = 'osetrovatel',
		COLUMN_ID = 'rodne_cislo',
        COLUMN_NAME = 'login',
		COLUMN_PASSWORD_HASH = 'heslo',
		COLUMN_ROLE = 'role';


	/** @var Nette\Database\Context */
	private $database;


	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}


	public function changeCredntials(array $credentials, $userId){
	    $this->database->table('osetrovatel')->where(self::COLUMN_ID, $userId)->update($credentials);
    }

	public function changePass($password, $userId){
	    $this->database->table('osetrovatel')->where('rodne_cislo', $userId)->update(['heslo'=> Nette\Security\Passwords::hash($password)]);
    }

    public function showTraining($rodne_cislo){
	    $ma_skoleni = $this->database->table('ma_skoleni')->where('rd_osetrovatel', $rodne_cislo);

	    $ret_array = array();

	    $i = 0;
        foreach ($ma_skoleni as $ma_skol)
        {
            $ret_array[$i] = array();
            $ret_array[$i]['nazev'] = array();
            $ret_array[$i]['nazev'] = $ma_skol->skoleni->nazev;
            $ret_array[$i]['datum'] = array();
            $ret_array[$i]['datum'] = $ma_skol->skoleni->datum;
            $ret_array[$i]['popis'] = array();
            $ret_array[$i]['popis'] = $ma_skol->skoleni->popis;
            $i++;
        }

        dump($ret_array);
        return $ret_array;
    }


	/**
	 * Performs an authentication.
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;
		$row = $this->database->table(self::TABLE_NAME)
			->where(self::COLUMN_NAME, $username)
			->fetch();

        $hash = Passwords::hash($row[self::COLUMN_PASSWORD_HASH]);

        if (!$row) {
			throw new Nette\Security\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);

		}
		if (!Nette\Security\Passwords::verify($password, $hash)) {
            if (!Nette\Security\Passwords::verify($password, $row[self::COLUMN_PASSWORD_HASH]))
			    throw new Nette\Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);

		}


		$arr = $row->toArray();
		unset($arr[self::COLUMN_PASSWORD_HASH]);
		return new Nette\Security\Identity($row[self::COLUMN_ID], $row[self::COLUMN_ROLE], $arr);
	}


	/**
	 * Adds new user.
	 * @param  string
	 * @param  string
	 * @param  string
	 * @return void
	 * @throws DuplicateNameException
	 */
	public function add($username, $email, $password)
	{
		try {
			$this->database->table(self::TABLE_NAME)->insert([
				self::COLUMN_NAME => $username,
				self::COLUMN_PASSWORD_HASH => Passwords::hash($password),
			]);
		} catch (Nette\Database\UniqueConstraintViolationException $e) {
			throw new DuplicateNameException;
		}
	}
}



class DuplicateNameException extends \Exception
{
}
