<?php
/**
 * SimpleChat add-on
 *
 * @package SocialStrap add-on
 * @author Milos Stojanovic
 * @copyright 2014 interactive32.com
 */

if (! defined('PUBLIC_PATH') || ! Zend_Auth::getInstance()->hasIdentity()) die('not allowed');

$max_lines = 15;
$max_chars = 500;

// switch stream 'On' on first load
$session = new Zend_Session_Namespace('Default');
$session->addon_simplechat_init = true;

$current_user = Zend_Auth::getInstance()->getIdentity();

$fn = realpath(dirname(__FILE__)) . "/data.json";
$data = json_decode(file_get_contents($fn));

$content = isset($_POST['simplechat-input']) && $_POST['simplechat-input'] ? trim($_POST['simplechat-input']) : '';

// strip slashes
$content = stripslashes($content);

// strip tags
$filter_st = new Zend_Filter_StripTags();
$content = $filter_st->filter($content);

// cut to max chars
$content = substr($content, 0, $max_chars);

if ($content) {

	$data[]	= array(
			'username' => $current_user->name,
			'screen_name' => $current_user->screen_name,
			'avatar' => $current_user->avatar,
			'timestamp' => time(),
			'text' => htmlspecialchars($content),
		);
	
	// cut to max size
	$data = array_slice($data, -1 * $max_lines);
	
	if (! is_writable($fn)) {
		echo json_encode('Error: file not writtable '.$fn);
		die;
	}
	
	// save to file
	file_put_contents($fn, json_encode($data));
}

// convet to object
$data = json_decode(json_encode($data), false);

// set view
$zview = $this;

require_once 'layout.php';

echo json_encode($html);
die;