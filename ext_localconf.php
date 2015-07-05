<?php
if (! defined ( 'TYPO3_MODE' ))
	die ( 'Access denied.' );

/**
 * Both views and model are provided using TYPO3 services.
 * Models should be
 * of the type 'cal_model' with a an extension key specific to that model.
 * Views can be of two types. The 'cal_view' type is used for views that
 * display multiple days. Within this type, subtypes for 'single', 'day',
 * 'week', 'month', 'year', and 'custom' are available. The default views
 * each have the key 'default'. Custom views tied to a specific model should
 * have service keys identical to the key of that model.
 */
	
/* Cal Example Concrete Model */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService ( $_EXTKEY, 'cal_event_model' /* sv type */,  'tx_cal_ts_service' /* sv key */,
	array (
		'title' => 'TypoScript Service for cal',
		'description' => '',
		'subtype' => 'event',
		'available' => TRUE,
		'priority' => 50,
		'quality' => 50,
		'os' => '',
		'exec' => '',
		'className' => 'TYPO3\\CMS\\CalTsService\\Service\\TypoScriptService' 
) );

/* Cal ttnews category Model */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService ( $_EXTKEY, 'cal_category_model' /* sv type */,  'tx_cal_ts_category_service' /* sv key */,
	array (
		'title' => 'TypoScript Category Service for cal',
		'description' => '',
		'subtype' => 'category',
		'available' => TRUE,
		'priority' => 50,
		'quality' => 50,
		'os' => '',
		'exec' => '',
		'className' => 'TYPO3\\CMS\\CalTsService\\Service\\CategoryService' 
) );
?>