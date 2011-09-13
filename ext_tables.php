<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if(t3lib_extMgm::isLoaded('tt_news')) {
	t3lib_extMgm::addStaticFile($_EXTKEY,'static/tt_news/','tt_news');	
}

if(t3lib_extMgm::isLoaded('seminars')) {
	t3lib_extMgm::addStaticFile($_EXTKEY,'static/tx_seminars/','tx_seminars');	
}

if(t3lib_extMgm::isLoaded('tt_products')) {
	t3lib_extMgm::addStaticFile($_EXTKEY,'static/tt_products/','tt_products');	
}

if(t3lib_extMgm::isLoaded('wec_sermons')) {
	t3lib_extMgm::addStaticFile($_EXTKEY,'static/wec_sermons/','wec_sermons');	
}

if(t3lib_extMgm::isLoaded('mbl_newsevent')) {
	t3lib_extMgm::addStaticFile($_EXTKEY,'static/mbl_newsevent/','mbl_newsevent');
}

t3lib_extMgm::addStaticFile($_EXTKEY,'static/birthday/','birthday');

?>