<?php 
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$_EXTKEY = 'cal_ts_service';

if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded ( 'tt_news' )) {
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile ( $_EXTKEY, 'Configuration/TypoScript/tt_news/', 'tt_news connector' );
}

if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded ( 'seminars' )) {
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile ( $_EXTKEY, 'Configuration/TypoScript/tx_seminars/', 'tx_seminars connector' );
}

if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded ( 'tt_products' )) {
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile ( $_EXTKEY, 'Configuration/TypoScript/tt_products/', 'tt_products connector' );
}

if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded ( 'wec_sermons' )) {
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile ( $_EXTKEY, 'Configuration/TypoScript/wec_sermons/', 'wec_sermons connector' );
}

if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded ( 'mbl_newsevent' )) {
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile ( $_EXTKEY, 'Configuration/TypoScript/mbl_newsevent/', 'mbl_newsevent connector' );
}

if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded ( 'sr_feuser_register' )) {
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile ( $_EXTKEY, 'Configuration/TypoScript/birthday/', 'Birthday connector' );
}

?>