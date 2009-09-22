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

require_once(PHOEBIUS_BASE_ROOT.'/usr/3rdparty/xmlrpc/lib/xmlrpc.inc');

/**
 * Some useful methods to work with LJ inteface via XMLRPC
 */
class LiveJournal
{
	private $lj;
	private $user;
	private $pass;
	private $incomingEnc='UTF-8';

	const CLIENT = 'dev.osiacat.ru';
	const OUTCOMING_ENCODING = 'UTF-8';

	function __construct($user,$pass)
	{
		$this->CreateClient();
		$this->SetUser($user);
		$this->SetPassword($pass);
	}

	function getIncomingEncoding()
	{
		return $this->incomingEnc;
	}

	function setIncomingEncoding($enc)
	{
		$this->incomingEnc = strtoupper($enc);
	}

	function setUser($user)
	{
		$this->user=$user;
	}

	function getUser()
	{
		return $this->user;
	}

	function setPassword($pass)
	{
		$this->pass=$pass;
	}

	function getPassword()
	{
		return $this->pass;
	}

	private function freshOutEncoding($s)
	{
		if ( $this->getIncomingEncoding() != self::OUTCOMING_ENCODING )
		{
			$s = mb_convert_encoding($s,self::OUTCOMING_ENCODING, $this->getIncomingEncoding());
		}
		return $s;
	}

	private function freshInEncoding($s)
	{
		if ( $this->getIncomingEncoding() != self::OUTCOMING_ENCODING )
		{
			$s = mb_convert_encoding($s, $this->getIncomingEncoding(), self::OUTCOMING_ENCODING);
		}
		return $s;
	}

	/**
	 * add a new entry to the specified livejournal
	 * @return array(itemid, anum), in order to calculate the itemid URL you have to use the
	 * following formula: itemid*256 + anum
	 */
	function aAddPost($subject, $content, $date, $tags=null, $nocomments=false)
	{
		//set message handler
		$message = new xmlrpcmsg('LJ.XMLRPC.postevent');

		//prepate message
		$message->addParam(php_xmlrpc_encode(array
		(
			"username"			=>	$this->getUser(),
			"hpassword"			=>	md5($this->getPassword()),
			"clientversion"		=>	self::CLIENT,
			"event"				=>	$this->freshOutEncoding("<lj-raw>".$content."</lj-raw>"),
			"subject"			=>	$this->freshOutEncoding($subject),
			"lineendings"		=>	"0x0A",
			"year"				=>	date("Y",$date),
			"mon"				=>	date("m",$date),
			"day"				=>	date("d",$date),
			"hour"				=>	date("H",$date),
			"min"				=>	date("i",$date),
			"ver"				=>  1,
			"props"				=>	array
			(
				"taglist"			=>	$this->FreshOutEncoding($tags),
				"opt_nocomments"	=>	(bool)$nocomments
			)
		)));

		//send the message to the server
		$response = $this->lj->send($message);

		//check what the reply is
		if ($response->value())
		{	# all cool
			$response = php_xmlrpc_decode($response->value());
			return $response;
		}
		return false;
	}

	/**
	 * updates an entry in the specified livejournal
	 */
	function updatePost($entryid, $subject, $content, $date, $tags=null, $nocomments=false)
	{
		//set message handler
		$message = new xmlrpcmsg('LJ.XMLRPC.editevent');

		//prepare content such that deletion works =)
		if ($content)
		{
			$content = "<lj-raw>".$content."</lj-raw>";
		}

		//prepate message
		$message->addParam(php_xmlrpc_encode(array
		(
			"username"			=>	$this->GetUser(),
			"hpassword"			=>	md5($this->GetPassword()),
			"clientversion"		=>	self::CLIENT,
			"event"				=>	$this->freshOutEncoding($content),
			"subject"			=>	$this->freshOutEncoding($subject),
			"lineendings"		=>	"0x0A",
			"year"				=>	date("Y",$date),
			"mon"				=>	date("m",$date),
			"day"				=>	date("d",$date),
			"hour"				=>	date("H",$date),
			"min"				=>	date("i",$date),
			"itemid"			=>	$entryid,
			"ver"				=>	1,
			"props"				=>	array
			(
				"taglist"			=>	$this->freshOutEncoding($tags),
				"opt_nocomments"	=>	(bool)$nocomments
			)
		)));


		//send the message to the server
		$response = $this->lj->send($message);
		//check what the reply is
		if ($response->value())
		{	# all cool
			return true;
		}
		return false;
	}

	/**
	 * deletes an entry in the specified livejournal
	 */
	function dropPost($itemid)
	{
		return $this->updatePost($itemid,null,null,time());
	}

	/**
	 * returns an array of posts from a selected LJ (max 50)
	 */
	function getPosts($beforedate_timestamp=null,$howmany=50)
	{
		$message = new xmlrpcmsg('LJ.XMLRPC.getevents');

		//prepare delim
		if (empty($beforedate_timestamp)){
			$beforedate_timestamp = date("Y-m-d H:i:s");
		}else{
			$beforedate_timestamp = date("Y-m-d H:i:s",$beforedate_timestamp);
		}

		//prepate message
		$message->addParam(php_xmlrpc_encode(array
		(
			"username"			=>	$this->GetUser(),
			"hpassword"			=>	md5($this->GetPassword()),
			"ver"				=>	1,
			"selecttype"		=>	"lastn",
			"beforedate"		=>	$beforedate_timestamp,
			"howmany"			=>	$howmany,
			"lineendings"		=>	"0x0A"
		)));

		//send the message to the server
		$response = $this->lj->send($message);

		//check what the reply is
		if ($response->value())
		{	# all cool
			$response = php_xmlrpc_decode($response->value());

			//utf decode events & pre-process raw data
			foreach($response['events'] as $key => $val)
			{
				$response['events'][$key]['event'] 		= $this->freshInEncoding($val['event']);
				$response['events'][$key]['subject'] 	= $this->freshInEncoding(strip_tags($val['subject']));
				$response['events'][$key]['eventtime'] 	= strtotime($val['eventtime']);

				//process props
				if (!$val['props']['opt_preformatted'])
				{
					$response['events'][$key]['event'] = nl2br($response['events'][$key]['event']);
				}

				//adjust comments on/off feature
				if ($val['props']['opt_nocomments'])
				{
					$response['events'][$key]['comments'] = 0;
				}else
				{
					$response['events'][$key]['comments'] = 1;
				}

				//adjust security mask
				/*
				if ($toget == 1){ //open & friends
					if ($response['events'][$key]['security'] == "private"){ //get rid of "private" posts
						unset($response['events'][$key]);
					}
				}elseif ($toget == 0){ //only open
					if ($response['events'][$key]['security'] == "private" or $response['events'][$key]['security'] == "usemask"){ //get rid of "private" and "friends" posts
						unset($response['events'][$key]);
					}
				}
				*/
			}

			return $response['events'];
		}
		return false;
	}

	/**
	 * returns an array of friend of from a selected LJ
	 */
	function friendOf()
	{
		$message = new xmlrpcmsg('LJ.XMLRPC.friendof');

		//prepate message
		$message->addParam(php_xmlrpc_encode(array
		(
			"username"			=>	$this->getUser(),
			"hpassword"			=>	md5($this->getPassword()),
			"ver"				=>	1
		)));

		//send the message to the server
		$response = $this->lj->send($message);

		//check what the reply is
		if ($response->value())
		{	# all cool
			$response = php_xmlrpc_decode($response->value());

			//utf decode events
			foreach($response['friendofs'] as $key => $val)
			{
				$response['friendofs'][$key]['fullname'] = $this->freshInEncoding($val['fullname']);
			}

			return $response['friendofs'];
		}
		return false;
	}


	/**
	 * returns an array of friends from a selected LJ
	 * @return array
	 */
	function getFriends()
	{
		$message = new xmlrpcmsg('LJ.XMLRPC.getfriends');

		//prepare message
		$message->addParam(php_xmlrpc_encode(array
		(
			"username"			=>	$this->GetUser(),
			"hpassword"			=>	md5($this->GetPassword()),
			"ver"				=>	1
		)));

		//send the message to the server
		$response = $this->lj->send($message);

		//check what the reply is
		if ($response->value())
		{	# all cool
			$response = php_xmlrpc_decode($response->value());

			//utf decode events
			foreach($response['friends'] as $key => $val)
			{
				$response['friends'][$key]['fullname'] = $this->freshInEncoding($val['fullname']);
			}

			return $response['friends'];
		}
		return false;
	}

	/**
	 * adds a list of friends to the selected LJ
	 */
	function addFriends(array $friends)
	{
		//start message
		$message = new xmlrpcmsg('LJ.XMLRPC.editfriends');

		//prepare friends list
		$userdata = array();
		foreach($friends as $user)
		{
			$userdata[] = new xmlrpcval(array
			(
				"username" => new xmlrpcval($user,'string')
			),
			'struct');
		}

		//prepare message
		$message->addParam(new xmlrpcval
		(
			array
			(
				"username" 			=> new xmlrpcval($this->GetUser(),'string'),
				"hpassword"			=> new xmlrpcval(md5($this->GetPassword()),'string'),
				"clientversion"		=> new xmlrpcval(self::CLIENT,'string'),
				"add"				=> new xmlrpcval($userdata,'array'),
			),
			'struct'
		));

		//send the message to the server
		$response = $this->lj->send($message);

		//check what the reply is
		if ($response->value())
		{	# all cool
			$response = php_xmlrpc_decode($response->value());
			return $response;
		}
		return false;
	}

	/**
	 * drops a list of friends from the selected LJ
	 */
	function dropFriends(array $friends)
	{
		//start message
		$message = new xmlrpcmsg('LJ.XMLRPC.editfriends');

		//prepare friends list
		$userdata = array();
		foreach($friends as $user)
		{
			$userdata[] = new xmlrpcval($user,'string');
		}

		//prepare message
		$message->addParam(new xmlrpcval
		(
			array
			(
				"username" 			=> new xmlrpcval($this->getUser(),'string'),
				"hpassword"			=> new xmlrpcval(md5($this->getPassword()),'string'),
				"clientversion"		=> new xmlrpcval(self::CLIENT,'string'),
				"delete"			=> new xmlrpcval($userdata,'array'),
			),
			'struct'
		));

		//send the message to the server
		$response = $this->lj->send($message);

		//check what the reply is
		if ($response->value())
		{	# all cool
			$response = php_xmlrpc_decode($response->value());
			return $response;
		}
		return false;
	}


	/**
	 * verifies if the password and user pair match with the LJ server
	 * @return boolean
	 */
	function login()
	{
		$message = new xmlrpcmsg('LJ.XMLRPC.login');

		//prepate message
		$message->addParam(php_xmlrpc_encode(array
		(
			"username"			=>	$this->getUser(),
			"hpassword"			=>	md5($this->getPassword()),
			"ver"				=>	1,
		)));

		//send the message to the server
		$response = $this->lj->send($message);

		//check what the reply is
		if ($response->value())
		{	# all cool
			return true;
		}

		return false;
	}

	/**
	 * initializes a livejournal RPC clien
	 */
	private function createClient(
		$server='www.livejournal.com', $interface='/interface/xmlrpc', $port=80)
	{
		$this->lj = new xmlrpc_client($interface, $server, $port);
		$GLOBALS['xmlrpc_internalencoding']='UTF-8';
	}
}

?>