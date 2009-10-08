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
 * Used for beautiful building of RSS 0.91+ feeds
 */
class RSSFeed
{

	/**
	 * RSS Version for output
	 */
	public $Version = "2.0";

	/**
	 * title of the channel
	 */
	public $Title;

	/**
	 * link to the channel
	 */
	public $Link;

	/**
	 * Channel charset
	 */
	public $CharSet = 'UTF-8';

	/**
	 * description of the channel
	 */
	public $Description;

	//optional Channel Data
	protected $optional = array
	(
	//	"ttl"       => 60,
		'generator' => 'Mojave-kernel/DataExport_RSS',
	//	'language'  => 'ru',
	//	'copyright' => "Copyright 2005-" . date("Y"),
	//	'managingEditor' => 'osiacat@osiacat.ru',
	//	'webMaster' => 'osiacat@osiacat.ru',
	);

	protected $latestTimestamp = 0;
	protected $items = array();

	function __construct($title, $link, $description, array $optional = array())
	{
		$this->Title = $title;
		$this->Link = $link;
		$this->Description = $description;
		$this->optional = array_merge($this->optional,$optional);

		//flush data
		$this->items = array();
	}

	function setLastBuildDate($ts = null)
	{
		$ts = $ts ? $ts : time();
		$this->optional['lastBuildDate'] = date('r', $ts);
		$this->latestTimestamp = $ts;
	}

	function getLastBuildDate()
	{
		return $this->latestTimestamp;
	}

	/**
	 * adds an Item to end of the feeder
	 */
	function addItem($title, $link, $description, $timestamp, array $optional = array())
	{
		$item = array
		(
			"title"			=> 	$title,
			"link"			=>	$link,
			"guid"          =>  array($link,array("isPermaLink" => "true")),
			"description"	=>	$description,
			"pubDate"       =>  date("r", $timestamp),
		);

		//RSS2.0 upgrade if needed
		$item = array_merge_recursive($item, $optional);

		$this->items[] = $item;

		$this->setLastBuildDate(max($this->latestTimestamp, $timestamp));
	}

	/**
	 * @var IWriter
	 */
	private $stream;

	function flush()
	{
		$ts = !$this->getLastBuildDate() ? time() : $this->getLastBuildDate();
		HTTPResponse::setLastModified($ts);

		$ctype = preg_match('/Mozilla/i', $_SERVER['USER_AGENT']) ? 'text/xml' : 'application/rss+xml';
		header('Content-Type: ' . $ctype . '; charset="' . $this->CharSet . '"', true);

		$this->stream = new StdOutWriter(false);
		$this->compile();
		$this->stream->dispose();
	}

	private function compile()
	{
		$channel = array
		(
			"title"			=>	strip_tags($this->Title),
			"link"			=>	$this->Link,
			"description"	=>	strip_tags($this->Description)
		);

		$this->optional = array_merge_recursive($this->optional, $channel);

		$this->stream->write('<?xml version="1.0" encoding="'.$this->CharSet.'"?>');
		$this->stream->write('<rss version="'.$this->Version.'" xmlns:content="http://purl.org/rss/1.0/modules/content/">');
		$this->stream->write('<channel>');
		$this->compileChannel();
		$this->compileItems();
		$this->stream->write('</channel>');
		$this->stream->write('</rss>');
	}

	/**
	 * creates an RSS XML file at the specified location
	 */
	function save($fileName)
	{
		$this->stream = new FileWriter($fileName);
		$this->compile();
		$this->stream->dispose();
	}

	/**
	 * return number of items in object
	 */
	function getItemCount()
	{
		return count($this->items);
	}

	/**
	 * return the items of the feeder
	 */
	function getItems()
	{
		return $this->items;
	}

	private function compileChannel()
	{
		return $this->parseNodes($this->optional);
	}

	private function compileItems()
	{
		foreach($this->items as &$conts)
		{
			$this->parseNode('item', array(), $conts);
		}
	}

	private function compileAttributes(array $obj)
	{
		if ( empty($obj) )
		{
			return "";
		}

		$attrs = array();
		foreach( $obj as $attrName => $attrVal )
		{
			$attrVal = htmlspecialchars($attrVal);
			$attrs[] = "{$attrName}=\"{$attrVal}\"";
		}
		return implode(" ", $attrs);
	}

	protected function parseNodes(array &$obj)
	{
		foreach($obj as $tagName => $nodeConts)
		{
			if ( is_array($nodeConts) )
			{
				if ( is_array(@$nodeConts[1]) )
				{
					$this->parseNode($tagName, $nodeConts[1], $nodeConts[0]);
				}
				else
				{
					$this->parseNodes($nodeConts);
				}
			}
			else
			{
				$this->parseNode($tagName, array(), $nodeConts);
			}
		}
	}

	protected function parseNode($tagName, array $attrs, &$contents)
	{
		$this->stream->write("<" . $tagName);
		if ( !empty($attrs) )
		{
			$this->stream->write(" ".$this->compileAttributes($attrs));
		}
		$this->stream->write(">");
		if ( is_array($contents) )
		{
			$this->parseNodes($contents);
		}
		else
		{
			$this->stream->write(htmlspecialchars($contents));
/*
			if ( $tagName != "title" )
			{
				$this->stream->write(str_replace("&amp;", "&", htmlspecialchars($contents)));
			}
			else
			{
				$this->stream->write(htmlspecialchars($contents));
			}
*/
		}
		$this->stream->write("</" . $tagName . ">");
	}
}

?>