<?php
namespace TYPO3\CMS\CalTsService\Service;
/**
 * *************************************************************
 * Copyright notice
 *
 * (c) 2005-2015 Mario Matzulla
 * All rights reserved
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
 * *************************************************************
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Base model for the category.  Provides basic model functionality that other
 * models can use or override by extending the class.  
 *
 * @author Mario Matzulla <mario@matzullas.de>
 * @package TYPO3
 * @subpackage cal
 */
class CategoryService extends \TYPO3\CMS\Cal\Service\CategoryService {
	
	var $key = '';
	
	public function __construct(){
		parent::__construct();
	}
	
	public function getCategorySearchString($pidList, $includePublic){
		return '';
	}
	
	/**
	 * Search for categories
	 */
	public function getCategoryArray($pidList, &$categoryArrayToBeFilled, $showPublicCategories=true){
		
		if($this->conf['display.']){
			foreach($this->conf['display.'] as $this->key => $conf){
				$this->thisConf = $conf;
				$this->categoryArrayByUid = Array ();
				$this->categoryArrayByEventUid = Array ();
				$this->categoryArrayByCalendarUid = Array ();
				$this->_getCategoryArray($pidList, $showPublicCategories);
				$categoryArrayToBeFilled[$this->key] = Array ($this->categoryArrayByUid,$this->categoryArrayByEventUid,$this->categoryArrayByCalendarUid);
			}
		}
	}
		
	/**
	 * Search for categories
	 */
	private function _getCategoryArray($pidList, $showPublicCategories=true){
		if($this->rightsObj->isLoggedIn() && $showPublicCategories){
			$feUserId = $this->rightsObj->getUserId();
		}else if($this->rightsObj->isLoggedIn()){
			$feUserId = $this->rightsObj->getUserId();
		}

		$categoryIds = Array ();
		$dbIds = Array ();
		$fileIds = Array ();
		$extUrlIds = Array ();
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
	}
	
	public function createCategory($row){
		return new \TYPO3\CMS\Cal\Model\CategoryModel ($row, $this->getServiceKey());
	}
	

}

?>