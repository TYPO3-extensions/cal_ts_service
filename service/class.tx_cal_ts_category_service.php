<?php
/***************************************************************
* Copyright notice
*
* (c) 2005 Foundation for Evangelism
* All rights reserved
*
* This file is part of the Web-Empowered Church (WEC)
* (http://webempoweredchurch.org) ministry of the Foundation for Evangelism
* (http://evangelize.org). The WEC is developing TYPO3-based
* (http://typo3.org) free software for churches around the world. Our desire
* is to use the Internet to help offer new life through Jesus Christ. Please
* see http://WebEmpoweredChurch.org/Jesus.
*
* You can redistribute this file and/or modify it under the terms of the
* GNU General Public License as published by the Free Software Foundation;
* either version 2 of the License, or (at your option) any later version.
*
* The GNU General Public License can be found at
* http://www.gnu.org/copyleft/gpl.html.
*
* This file is distributed in the hope that it will be useful for ministry,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* This copyright notice MUST APPEAR in all copies of the file!
***************************************************************/

require_once(t3lib_extMgm::extPath('cal').'service/class.tx_cal_category_service.php');

/**
 * Base model for the category.  Provides basic model functionality that other
 * models can use or override by extending the class.  
 *
 * @author Mario Matzulla <mario@matzullas.de>
 * @package TYPO3
 * @subpackage cal
 */
class tx_cal_ts_category_service extends tx_cal_category_service {
	
	var $key = '';
	
	function tx_cal_ts_category_service(){
		$this->tx_cal_category_service();
	}
	
	function getCategorySearchString($pidList, $includePublic){
		return '';
		if($this->conf['category']!=''){
			$categorySearchString .= ' AND tt_news_cat_mm.uid_foreign IN ('.$this->conf['category'].')';
		}
		return $categorySearchString;
	}
	
	/**
	 * Search for categories
	 */
	function getCategoryArray($pidList, &$categoryArrayToBeFilled, $showPublicCategories=true){
		
		if($this->conf['display.']){
			foreach($this->conf['display.'] as $this->key => $conf){
				$this->thisConf = $conf;
				$this->categoryArrayByUid = array();
				$this->categoryArrayByEventUid = array();
				$this->categoryArrayByCalendarUid = array();
				$this->_getCategoryArray($pidList, $showPublicCategories);
				$categoryArrayToBeFilled[$this->key] = array($this->categoryArrayByUid,$this->categoryArrayByEventUid,$this->categoryArrayByCalendarUid);
			}
		}
	}
		
	/**
	 * Search for categories
	 */
	function _getCategoryArray($pidList, $showPublicCategories=true){
		if($this->rightsObj->isLoggedIn() && $showPublicCategories){
			$feUserId = $this->rightsObj->getUserId();
		}else if($this->rightsObj->isLoggedIn()){
			$feUserId = $this->rightsObj->getUserId();
		}

		$categoryIds = array();
		$dbIds = array();
		$fileIds = array();
		$extUrlIds = array();
		if($this->thisConf['cat_select.']){
			$this->thisConf['cat_select.']['pidInList'] = $pidList;
			if($this->thisConf['enableLocalizationAndVersioningCat']){
				$this->thisConf['cat_select.']['andWhere'] .= $this->getAdditionalWhereForLocalizationAndVersioning($this->thisConf['catTable']);
			}
			$queryArray = $this->cObj->getQuery($this->thisConf['catTable'],$this->thisConf['cat_select.'],true);
			$result = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryArray);
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
				if($this->thisConf['enableLocalizationAndVersioningCat']){
					if ($GLOBALS['TSFE']->sys_language_content) {
						$row = $GLOBALS['TSFE']->sys_page->getRecordOverlay($this->thisConf['catTable'], $row, $GLOBALS['TSFE']->sys_language_content, $GLOBALS['TSFE']->sys_language_contentOL, '');
					}
					if ($this->versioningEnabled) {
						// get workspaces Overlay
						$GLOBALS['TSFE']->sys_page->versionOL($this->thisConf['catTable'],$row);
					}
				}
				if(!$this->categoryArrayByUid[$row['uid']]){
					$category = $this->createCategory($row);
					$category->setHeaderStyle($this->thisConf['headerStyle']);
					$category->setBodyStyle($this->thisConf['bodyStyle']);
					if($row['uid_local']){
						$this->categoryArrayByEventUid[$row['uid_local']] = $category;
					}
					$this->categoryArrayByUid[$row['uid']] = $category;
					$this->categoryArrayByCalendarUid['0'.'###'.$this->thisConf['legendDescription']][] = $category->getUid();
				}
			}
		}
		return;
	}
	
	function createCategory($row){
		return t3lib_div::makeInstance('tx_cal_category_model',$row, $this->getServiceKey());
	}
	

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cal_ts_service/service/class.tx_cal_ts_category_service.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cal_ts_service/service/class.tx_cal_ts_category_service.php']);
}
?>