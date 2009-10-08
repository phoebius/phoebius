<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright notice
 *
 ************************************************************************************************/

/**
 * TODO mergeData(array $data)
 */
class CookieStorage
{
	const SIGN_LENGTH = 10; // max is 40 'cause we use SHA1 hashing
	const COOKIE_SIZE = 4096; // RFC

	private $update = false;

	private $basekey;
	private $key;

	private $cryptor = null;

	private $data = false;
	private $expires = 0;

	function __construct($key)
	{
		//TODO ClientRequest
		Assert::isFalse(
			headers_sent() || Response::isFinished(),
			'Yup! Headers are already sent so the storage cannot work properly.'
		);

		ob_start();
		$this->basekey = $key . Session::userSupertag(true);
		$this->key = $this->sign($key);
	}

	/**
	 * @return CookieStorage
	 */
	static function create($key)
	{
		return new self($key);
	}

	function getKey()
	{
		return $this->basekey;
	}

	function getStorageLifetime()
	{
		return $this->expires - ( !$this->expires ? 0 : time() );
	}

	/**
	 * @return CookieStorage
	 */
	function setStorageLifetime($lifetime = 0)
	{
		$this->expires = $lifetime + ( !$lifetime ? 0 : time() );

		return $this;
	}

	private function store()
	{
		$data = $this->encryptArray($this->data);
		$data = $this->terminateString($data);

		$this->split2cookies($data);
	}

	private function split2cookies($string)
	{
		$data = str_split($string, self::COOKIE_SIZE);
		$size = sizeof($data);

		for( $i = 0; $i < $size; $i++ )
		{
			$this->setCookie($this->getSliceCookieName($i), $data[$i]);
		}

		$this->setCookie($this->getNumberCookieName(), $this->terminateString($size));
	}

	private function getNumberCookieName()
	{
		return $this->key . 'Sz';
	}

	private function getSliceCookieName($n)
	{
		return $this->key . $n;
	}

	private function joinFromCookies()
	{
		// get the number of cookies
		$size = $this->getTerminatedString(@$_COOKIE[$this->getNumberCookieName()]);
		if (!$size)
		{
			return false;
		}

		// get the encrypted data itself
		$data = array();
		for( $i = 0; $i < $size; $i++ )
		{
			$data[] = $_COOKIE[$this->getSliceCookieName($i)];
		}
		$string = join("", $data);

		return $string;
	}

	private function terminateString($string)
	{
		return $string . $this->sign($string);
	}

	private function getTerminatedString($t_string)
	{
		$string = substr($t_string, 0, -1 * self::SIGN_LENGTH);
		$check = substr($t_string, -1 * self::SIGN_LENGTH);

		if ($this->sign($string) == $check)
		{
			return $string;
		}
		else
		{
			return false;
		}
	}

	/**
	 * @return CookieStorage
	 */
	function clear()
	{
		$this->setData(array());

		return $this;
	}

	/**
	 * @return CookieStorage
	 */
	function setData(array $data)
	{
		$this->data = $data;
		$this->update = true;

		return $this;
	}

	/**
	 * @throws WrongStateException
	 */
	function getData()
	{
		if (!$this->data)
		{
			$this->restore();
		}

		return $this->data;
	}

	private function restore()
	{
		do
		{
			$string = $this->joinFromCookies();
			$encryptedString = $this->getTerminatedString($string);

			if (!$encryptedString)
			{
				$message = 'data is invalid';
				break;
			}

			$array = $this->decryptArray($encryptedString);
			if ( !is_array($array) )
			{
				$message = 'data cannot be decrypted';
				break;
			}

			$this->data = $array;

			return;

		} while(false);

		throw new WrongStateException("Storage is corrupted ($message)");
	}

	protected function setCookie($key, $data)
	{
		setcookie($key, $data, $this->expires, '/');
	}

	/**
	 * @return ICryptor
	 */
	protected function getCryptor()
	{
		if (is_null($this->cryptor))
		{
			$s = $this->sign($this->key);
			$this->cryptor = new XORCryptor($s);
		}
		return $this->cryptor;
	}

	private function encryptArray(array $data)
	{
		$string = serialize($data);
		$s = $this->getCryptor()->encrypt($string);
		return $s;
	}

	private function decryptArray($encryptedString)
	{
		$s = $this->getCryptor()->decrypt($encryptedString);
		$s = unserialize($s);
		return $s;
	}

	private function sign($string)
	{
		$start = ( 40 - self::SIGN_LENGTH ) / 2;
		$s = sha1($string . __FILE__ . $this->basekey . get_class($this));
		return substr($s, floor($start), self::SIGN_LENGTH);
	}

	function __destruct()
	{
		if ( is_array($this->data) && $this->update )
		{
			$this->store();
		}
	}
}

?>