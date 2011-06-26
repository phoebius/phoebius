<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 Scand Ltd.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU Lesser General Public License as published by the Free Software Foundation;
 * either version 3 of the License, or (at your option) any later version.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * this program; if not, see <http://www.gnu.org/licenses/>.
 *
 ************************************************************************************************/

/**
 * Cookie storage
 *
 * @ingroup App
 */
class Session
{
	const CHUNK_SIZE = 4096;
	
	private $sessionId;
	private $response;
	private $data = array();
	
	function __construct($sessionId, WebResponse $response)
	{
		$this->sessionId = $sessionId;
		$this->response = $response;
		$this->chunkId = $this->getChunkId(0, 0);
	}
	
	function __get($name)
	{
		if(isset($this->data[$name])) 
			return $this->data[$name];
	}
	
	function __set($name, $value)
	{
		$this->data[$name] = $value;
	}
	
	function import(array $dataChunks)
	{
		if (!isset($dataChunks[$this->chunkId])) 
			return;
		
		$length = $dataChunks[$this->chunkId];
		$s = '';
		
		for ($i = 0; $i < $length; $i++) {
			$chunkId = $this->getChunkId($i, $length);
			
			if (!isset($dataChunks[$chunkId])) 
				return;

			$s .= $dataChunks[$chunkId];
		}
		
		try {
			$data = $this->decrypt($s);
		}
		catch (Exception $e) {
			return;
		}
		
		$this->data = $data;
	}
	
	function save($ttl = null)
	{
		foreach ($this->split() as $key => $value) {
			$this->response->setCookie(new Cookie($key, $value, $ttl));
		}
	}
	
	private function split()
	{
		$chunks = str_split($this->encrypt(), self::CHUNK_SIZE);
		$dataChunks = array();
		foreach ($chunks as $i => $chunk) {
			$dataChunks[$this->getChunkId($i, sizeof($chunks))] = $chunk;
		}
		
		// sign
		$dataChunks[$this->chunkId] = sizeof($chunks);
		
		return $dataChunks;
	}
	
	private function getChunkId($chunkPos, $chunksNumber)
	{
		return substr(sha1($this->sessionId.$chunkPos.$chunksNumber), 0, 4);
	}
	
	private function encrypt() 
	{
		$c = new XorCipherer($this->sessionId);
		return $c->encrypt(serialize($this->data));
	}
	
	private function decrypt($s)
	{
		$c = new XorCipherer($this->sessionId);
		return unserialize($c->decrypt($s));
	}
}

?>