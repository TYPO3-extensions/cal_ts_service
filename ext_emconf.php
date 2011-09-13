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
	'title' => 'Typoscript Service for Calendar Base',
	'description' => 'Connects external tables to Calendar Base through a Typoscript-based configuration.',
	'category' => 'plugin',
	'shy' => 0,
	'dependencies' => 'cal',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author' => 'Mario Matzulla',
	'author_email' => 'mario@matzullas.de',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'version' => '1.2-dev',
	'constraints' => array(
		'depends' => array(
			'cal' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:23:{s:9:"ChangeLog";s:4:"c4c3";s:12:"ext_icon.gif";s:4:"35b9";s:17:"ext_localconf.php";s:4:"e718";s:14:"ext_tables.php";s:4:"a833";s:22:"icon_tx_cal_events.gif";s:4:"475a";s:13:"locallang.xml";s:4:"6ad6";s:14:"doc/manual.sxw";s:4:"6289";s:31:"model/class.tx_cal_ts_model.php";s:4:"eee1";s:44:"service/class.tx_cal_ts_category_service.php";s:4:"03fa";s:35:"service/class.tx_cal_ts_service.php";s:4:"96ff";s:29:"static/birthday/constants.txt";s:4:"4569";s:25:"static/birthday/setup.txt";s:4:"7424";s:34:"static/mbl_newsevent/constants.txt";s:4:"77f9";s:30:"static/mbl_newsevent/setup.txt";s:4:"916c";s:28:"static/tt_news/constants.txt";s:4:"bd51";s:24:"static/tt_news/setup.txt";s:4:"a678";s:32:"static/tt_products/constants.txt";s:4:"af8e";s:28:"static/tt_products/setup.txt";s:4:"3347";s:32:"static/tx_seminars/constants.txt";s:4:"6a19";s:28:"static/tx_seminars/setup.txt";s:4:"fb77";s:32:"static/wec_sermons/constants.txt";s:4:"a1ff";s:28:"static/wec_sermons/setup.txt";s:4:"96d2";s:16:"template/ts.tmpl";s:4:"477f";}',
	'suggests' => array(
	),
);

?>