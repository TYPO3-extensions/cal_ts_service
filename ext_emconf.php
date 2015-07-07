<?php

########################################################################
# Extension Manager/Repository config file for ext "cal_ts_service".
#
# Auto generated 03-05-2011 13:45
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'TypoScript Service for Calendar Base',
	'description' => 'Connects external tables to Calendar Base through a TypoScript-based configuration.',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '2.0.0',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'clearCacheOnLoad' => 0,
	'author' => 'Mario Matzulla',
	'author_email' => 'mario@matzullas.de',
	'constraints' => array(
		'depends' => array(
			'typo3' => '6.1.0-7.9.99',
			'cal' => '1.9.0-'
		),
	),
);

?>