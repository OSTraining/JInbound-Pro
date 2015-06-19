<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// prevent timezone not set warnings to appear all over,
// for PHP 5.3.3+
$oldLevel = error_reporting(0);
$serverTimezone = date_default_timezone_get();
error_reporting($oldLevel);
date_default_timezone_set( $serverTimezone);

$target = 'changelog/changelog.src.log';

// arguments
$args = arguments($argv);

foreach( $args['options'] as $option) {
	$$option[0] = $option[1];
}

foreach (array('user', 'repo', 'username', 'password') as $var)
{
	if (!isset($$var))
	{
		throw new Exception("Variable $var not found");
	}
}

$done    = false;
$max     = isset($max) ? (int) $max : 100;
$iter    = 0;
$issues  = array();
$url     = "https://api.github.com/repos/$user/$repo/issues?filter=all&state=closed&labels=CL";
$options = array('http' => array(
	'header' => array(
		'User-Agent: PHP5',
		"Authorization: Basic " . base64_encode("$username:$password")
	)
));

while (!$done)
{
	if ($iter++ > $max)
	{
		throw new Exception('Too many iterations');
	}
	$context  = stream_context_create($options);
	$response = file_get_contents($url, false, $context);
	$stack    = json_decode($response);
	if (!is_array($stack))
	{
		throw new Exception('Bad response');
	}
	foreach ($stack as $issue)
	{
		$issues[] = $issue;
	}
	$next = false;
	foreach ($http_response_header as $header)
	{
		if ('Link:' !== substr($header, 0, 5))
		{
			continue;
		}
		$has_link = false;
		$parts = explode(',', substr($header, 6));
		foreach ($parts as $part)
		{
			$part = trim($part);
			list($part_url, $part_rel) = explode(';', $part, 2);
			$part_url = str_replace(array('<', '>'), '', $part_url);
			if (false !== strpos($part_rel, 'next'))
			{
				$next = $part_url;
				$has_link = true;
				break;
			}
		}
		if ($has_link)
		{
			break;
		}
	}
	if ($next)
	{
		$url = $next;
	}
	else
	{
		$done = true;
	}
}

echo "Found " . count($issues) . " issues\n";
file_put_contents($target, json_encode($issues));

// read arguments from command line
// taken from php.net
function arguments($args ) {
	$ret = array(
				'exec'			=> '',
				'options'	 => array(),
				'flags'		 => array(),
				'arguments' => array(),
	);

	$ret['exec'] = array_shift( $args );

	while (($arg = array_shift($args)) != NULL) {
		// Is it a option? (prefixed with --)
		if ( substr($arg, 0, 2) === '--' ) {
			$option = substr($arg, 2);

			// is it the syntax '--option=argument'?
			if (strpos($option,'=') !== FALSE)
			array_push( $ret['options'], explode('=', $option, 2) );
			else
			array_push( $ret['options'], $option );
			 
			continue;
		}

		// Is it a flag or a serial of flags? (prefixed with -)
		if ( substr( $arg, 0, 1 ) === '-' ) {
			for ($i = 1; isset($arg[$i]) ; $i++)
			$ret['flags'][] = $arg[$i];

			continue;
		}

		// finally, it is not option, nor flag
		$ret['arguments'][] = $arg;
		continue;
	}
	return $ret;
}//function arguments
