<?php
/**
 * Simple SVN changelog formatter
 *
 * Accepted tags:
 * -NEW- new feature
 * -CHG- change
 * -BUG- bug
 * -REL- release
 * -EXC- exclude from log
 * none exlude from log
 *
 */


// various
$nl = "\n";
$relString = ' [ %s ] %s';
$itemFirstLine = "    [%s]    %s";
$itemOtherLine = "             %s";

// allowed tags - commits comments with any other tag, or without any tag
// will not be included in the changelog
$tags = array (
	'NEW' => 'CL - feature', 'BUG' => 'CL - bug', 'CHG' => 'CL - change', 'REL' => 'CL - release'
);

// prevent timezone not set warnings to appear all over,
// for PHP 5.3.3+
$oldLevel = error_reporting(0);
$serverTimezone = date_default_timezone_get();
error_reporting($oldLevel);
date_default_timezone_set( $serverTimezone);

// arguments
$args = arguments($argv);
//var_dump($args);

// source and target, replace by read from command line
$source = 'changelog/changelog.src.log';
$target = 'changelog/changelog.log';

// milestone prefix
$milestone_prefix = '';

// changelog title and build, to read from cli
if(empty( $args['options'])) {
	echo 'Bad arguments, syntax: --title="JInbound changelog" --build=880. Exiting';
	exit();
}

foreach( $args['options'] as $option) {
	$$option[0] = $option[1];
}

if(empty($title) || empty( $build)) {
	echo 'Bad arguments, syntax: --title="JInbound changelog" --build=880. Exiting';
	exit();

}

if(empty($linelength)) {
	$linelength = 80;
}
$sep = str_pad( '-', $linelength, '-', STR_PAD_BOTH) . $nl;

// read log file
if (!file_exists($source)) {
	echo 'No log file found in ' . $source;
	exit();
}
$rawlog = file_get_contents($source);

if (empty($rawlog)) {
	echo 'No log entry found in ' . $source;
	exit();
}

echo "Processing: " . strlen($rawlog) . " characters ...\n";

$entries = json_decode(trim($rawlog));

$out = '';

// title
$out .= str_pad( $title, $linelength, ' ', STR_PAD_BOTH) . $nl . $nl;
//date + build
$date = new DateTime();
$dateBuild = $date->format( 'Y-m-d H:i') . ' - build #' . $build;
$out .= str_pad( '(' . $dateBuild . ')', $linelength, ' ', STR_PAD_BOTH) . $nl . $nl;

$milestone = false;
$started = false;

if (empty($entries)) {
	echo 'Entries is empty';
	exit();
}
if (!is_array($entries)) {
	echo 'No Entries ';
	print_r($entries);
	exit();
}

// NOTE: using milestones as versions, cannot rely on them being in order!
$versions = array();

foreach($entries as $entry) {
	// check the tag
	if (empty($entry->labels)) {
		continue;
	}
	$entryTag = false;
	foreach ($entry->labels as $label) {
		if (in_array($label->name, $tags)) {
			$entryTag = $label->name;
			break;
		}
	}
	if (empty($entryTag)) {
		continue;
	}
	$entryTag = array_search($entryTag, $tags);
	if (!in_array($entryTag, array_keys($tags))) {
		continue;
	}
	
	if (!is_object($entry->milestone)) {
		continue;
	}
	
	$version_key = $entry->milestone->title;
	if (!array_key_exists($version_key, $versions)) {
		$date = new DateTime($entry->milestone->due_on);
		$versions[$version_key] = array(
			'date'    => $date->format('Y-m-d')
		,	'title'   => $entry->milestone->title
		,	'entries' => array()
		);
	}
	
	$date = new DateTime($entry->created_at);
	
	$msg = formatMsg($entry->title, $linelength - strlen( $itemOtherLine));
	foreach( $msg as $nbr => $line) {
		if( $nbr == 0) {
			$versions[$version_key]['entries'][] = $nl . sprintf( $itemFirstLine, strtolower($entryTag), $msg[$nbr]);
		} else {
			$versions[$version_key]['entries'][] = $nl . sprintf( $itemOtherLine,	$msg[$nbr]);
		}
	}
}

foreach ($versions as $version => $version_data) {
	if (!empty($milestone_prefix))
	{
		if (0 !== strpos($version_data['title'], $milestone_prefix))
		{
			continue;
		}
		$version_data['title'] = str_replace($milestone_prefix, '', $version_data['title']);
	}
	else if (!preg_match('/^[0-9]+\.[0-9]+\.[0-9]+$/', $version_data['title']))
	{
		continue;
	}
	$out .= $nl . $nl . $sep;
	$out .= sprintf($relString, $version_data['date'], $version_data['title']);
	$out .= $nl . $sep . $nl;
	foreach ($version_data['entries'] as $entry) {
		$out .= $entry;
	}
}

// write to target file
file_put_contents( $target, $out);
echo 'done';

// breaks down a commit message into a array of strings
// readu

/**
 * Breaks down a commit message into an array of strings
 * ready for inclusion into a changelog file
 * Message can be multiline, so first it is broken into
 * each individual lines. Then each line is broken down
 * to individual words. These words are in turn
 * aggregated again to form lines that are $length 
 * characters in lenght at most.
 * 
 * @param $msg
 * @param $length
 */
function formatMsg( $msg, $length) {
	$newMsg = array();

	$msgs = explode( "\n", $msg);
	$line = 0;
	foreach( $msgs as $subMsg) {
		$subMsg = trim( $subMsg);
		if(!empty($subMsg)) {
			$bits = explode( ' ', $subMsg);

			$done = false;
			$bitNbr = 0;
			do {
				$newMsg[$line] = '';
				do {
					$bits[$bitNbr] = trim($bits[$bitNbr]);
					if (!empty($bits[$bitNbr])) {
						$newMsg[$line] .= trim($bits[$bitNbr]);
					};
					$next = empty( $bits[$bitNbr+1]) ? '' : trim($bits[$bitNbr+1]);
					if( empty( $next) || strlen($newMsg[$line] . ' ' . $next) > $length) {
						$nextLine = true;
					} else {
						$newMsg[$line] .= ' ';
						$nextLine = false;
					}
					$bitNbr++;
				} while ( !$nextLine);
				$line++;
				$done = empty( $bits[$bitNbr]);
			} while (!$done);
		}
	}

	return $newMsg;
}

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
