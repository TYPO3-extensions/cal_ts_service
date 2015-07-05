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
 * This model fetches all tt_news between a start- and endtime or by uid.
 *
 * @author Mario Matzulla <mario@matzullas.de>
 * @package TYPO3
 * @subpackage cal
 */
class TypoScriptService extends \TYPO3\CMS\Cal\Service\EventService {
	
	var $thisConf;
	var $key = '';
	
	/**
	 * Finds all events.
	 *
	 * @return array array of events represented by the model.
	 */
	public function findAllWithin($start_date, $end_date, $pidList) {
		$this->setStartAndEndPoint ( $start_date, $end_date );
		$events = Array ();
		if ($this->conf ['display.']) {
			foreach ( $this->conf ['display.'] as $this->key => $conf ) {
				$this->thisConf = $conf;
				$eventsFromService = $this->_findAllWithin ( $start_date->getDate ( DATE_FORMAT_UNIXTIME ), $end_date->getDate ( DATE_FORMAT_UNIXTIME ), $pidList );
				$this->mergeEvents ( $events, $eventsFromService );
			}
		}
		
		return $events;
	}
	
	private function _findAllWithin($start_date, $end_date, $pidList) {
		$events = Array ();
		
		// with categories
		$service = &$this->getCategoryService ();
		$categories = Array ();
		$service->getCategoryArray ( $pidList, $categories );
		$processedUids = Array (0);
		$where = $this->cObj->substituteMarkerArrayCached ( $this->thisConf ['findAllWithinWhere'], Array (
				'###START###' => $start_date,
				'###END###' => $end_date 
		), Array (), Array () );
		if ($this->thisConf ['enableLocalizationAndVersioning']) {
			$where .= $this->getAdditionalWhereForLocalizationAndVersioning ( $this->thisConf ['pidTable'] );
		}
		if ($this->thisConf ['event_select_with_cat.']) {
			$this->thisConf ['event_select_with_cat.'] ['pidInList'] = $pidList;
			$this->thisConf ['event_select_with_cat.'] ['andWhere'] = $where;
			$queryArray = $this->cObj->getQuery ( $this->thisConf ['pidTable'], $this->thisConf ['event_select_with_cat.'], true );
			
			$result = $GLOBALS ['TYPO3_DB']->exec_SELECT_queryArray ( $queryArray );
			
			$event = null;
			while ( $row = $GLOBALS ['TYPO3_DB']->sql_fetch_assoc ( $result ) ) {
				if (! in_array ( $row ['uid'], $processedUids )) {
					if ($this->thisConf ['enableLocalizationAndVersioning']) {
						if ($GLOBALS ['TSFE']->sys_language_content) {
							$row = $GLOBALS ['TSFE']->sys_page->getRecordOverlay ( $this->thisConf ['pidTable'], $row, $GLOBALS ['TSFE']->sys_language_content, $GLOBALS ['TSFE']->sys_language_contentOL, '' );
						}
						if ($this->versioningEnabled) {
							// get workspaces Overlay
							$GLOBALS ['TSFE']->sys_page->versionOL ( $this->thisConf ['pidTable'], $row );
						}
					}
					$row ['ts_key'] = $this->key;
					$row [$this->thisConf ['startTimeField']] += \TYPO3\CMS\Cal\Utility\Functions::strtotimeOffset ( $row [$this->thisConf ['startTimeField']] );
					if ($this->thisConf ['endTimeField']) {
						$row [$this->thisConf ['endTimeField']] += \TYPO3\CMS\Cal\Utility\Functions::strtotimeOffset ( $row [$this->thisConf ['endTimeField']] );
					}
					$event = $this->createEvent ( $row, false );
					if ($this->extConf ['useNewRecurringModel']) {
						$this->recurringEvent ( $event );
						$events_tmp = $this->getRecurringEventsFromIndex ( $event );
					} else {
						$events_tmp = $this->recurringEvent ( $event );
					}
					if (! empty ( $events )) {
						$this->mergeEvents ( $events, $events_tmp );
					} else {
						$events = $events_tmp;
					}
					$processedUids [] = $row ['uid'];
				}
				if ($categories [$this->key] [0] [$row ['uid_foreign']]) {
					$event->addCategory ( $categories [$this->key] [0] [$row ['uid_foreign']] );
				}
			}
		}
		// without categories
		$this->thisConf ['event_select.'] ['pidInList'] = $pidList;
		$this->thisConf ['event_select.'] ['andWhere'] = $where . ' AND ' . $this->thisConf ['pidTable'] . '.uid NOT IN (' . implode ( ',', $processedUids ) . ')';
		$queryArray = $this->cObj->getQuery ( $this->thisConf ['pidTable'], $this->thisConf ['event_select.'], true );
		
		$result = $GLOBALS ['TYPO3_DB']->exec_SELECT_queryArray ( $queryArray );
		$processedUids = Array ();
		
		while ( $row = $GLOBALS ['TYPO3_DB']->sql_fetch_assoc ( $result ) ) {
			if (! in_array ( $row ['uid'], $processedUids )) {
				if ($this->thisConf ['enableLocalizationAndVersioning']) {
					if ($GLOBALS ['TSFE']->sys_language_content) {
						$row = $GLOBALS ['TSFE']->sys_page->getRecordOverlay ( $this->thisConf ['pidTable'], $row, $GLOBALS ['TSFE']->sys_language_content, $GLOBALS ['TSFE']->sys_language_contentOL, '' );
					}
					if ($this->versioningEnabled) {
						// get workspaces Overlay
						$GLOBALS ['TSFE']->sys_page->versionOL ( $this->thisConf ['pidTable'], $row );
					}
				}
				$row ['ts_key'] = $this->key;
				$row [$this->thisConf ['startTimeField']] += \TYPO3\CMS\Cal\Utility\Functions::strtotimeOffset ( $row [$this->thisConf ['startTimeField']] );
				if ($this->thisConf ['endTimeField']) {
					$row [$this->thisConf ['endTimeField']] += \TYPO3\CMS\Cal\Utility\Functions::strtotimeOffset ( $row [$this->thisConf ['endTimeField']] );
				}
				$event = $this->createEvent ( $row, false );
				
				if ($this->extConf ['useNewRecurringModel']) {
					$this->recurringEvent ( $event );
					$events_tmp = $this->getRecurringEventsFromIndex ( $event );
				} else {
					$events_tmp = $this->recurringEvent ( $event );
				}
				if (! empty ( $events )) {
					$this->mergeEvents ( $events, $events_tmp );
				} else {
					$events = $events_tmp;
				}
				$processedUids [] = $row ['uid'];
			}
		}
		return $events;
	}
	
	/**
	 * Finds all events.
	 *
	 * @return array array of events represented by the model.
	 */
	public function findAll($pidList) {
		$events = Array ();
		if ($this->conf ['display.']) {
			foreach ( $this->conf ['display.'] as $this->key => $conf ) {
				$this->thisConf = $conf;
				$eventsFromService = $this->_findAll ( $pidList );
				$this->mergeEvents ( $events, $eventsFromService );
			}
		}
		return $events;
	}
	
	private function _findAll($pidList) {
		$events = Array ();
		
		// Find records with categories
		$service = &$this->getCategoryService ();
		$categories = Array ();
		$service->getCategoryArray ( $pidList, $categories );
		
		$where = $this->cObj->substituteMarkerArrayCached ( $this->thisConf ['findAll'], Array (
				'###START###' => $start_date,
				'###END###' => $end_date 
		), Array (), Array () );
		
		if ($this->thisConf ['enableLocalizationAndVersioning']) {
			$where .= $this->getAdditionalWhereForLocalizationAndVersioning ( $this->thisConf ['pidTable'] );
		}
		
		$this->thisConf ['event_select_with_cat.'] ['pidInList'] = $pidList;
		$this->thisConf ['event_select_with_cat.'] ['andWhere'] = $where;
		$queryArray = $this->cObj->getQuery ( $this->thisConf ['pidTable'], $this->thisConf ['event_select_with_cat.'], true );
		$result = $GLOBALS ['TYPO3_DB']->exec_SELECT_queryArray ( $queryArray );
		$processedUids = Array (0);
		
		while ( $row = $GLOBALS ['TYPO3_DB']->sql_fetch_assoc ( $result ) ) {
			if (! in_array ( $row ['uid'], $processedUids )) {
				if ($this->thisConf ['enableLocalizationAndVersioning']) {
					if ($GLOBALS ['TSFE']->sys_language_content) {
						$row = $GLOBALS ['TSFE']->sys_page->getRecordOverlay ( $this->thisConf ['pidTable'], $row, $GLOBALS ['TSFE']->sys_language_content, $GLOBALS ['TSFE']->sys_language_contentOL, '' );
					}
					if ($this->versioningEnabled) {
						// get workspaces Overlay
						$GLOBALS ['TSFE']->sys_page->versionOL ( $this->thisConf ['pidTable'], $row );
					}
				}
				$row ['ts_key'] = $this->key;
				$row [$this->thisConf ['startTimeField']] += \TYPO3\CMS\Cal\Utility\Functions::strtotimeOffset ( $row [$this->thisConf ['startTimeField']] );
				if ($this->thisConf ['endTimeField']) {
					$row [$this->thisConf ['endTimeField']] += \TYPO3\CMS\Cal\Utility\Functions::strtotimeOffset ( $row [$this->thisConf ['endTimeField']] );
				}
				$event = $this->createEvent ( $row, false );
				$events [gmdate ( 'Ymd', $row [$this->thisConf ['startTimeField']] )] [(gmdate ( 'Hi', $row [$this->thisConf ['startTimeField']] + $this->thisConf ['defaultLength'] ))] [$row ['uid']] = $event;
				$processedUids [] = $row ['uid'];
			}
			if ($categories [$this->key] [0] [$row ['uid_foreign']]) {
				$event->addCategory ( $categories [$this->key] [0] [$row ['uid_foreign']] );
			}
		}
		
		// Find records without categories
		$this->thisConf ['event_select.'] ['pidInList'] = $pidList;
		$this->thisConf ['event_select.'] ['andWhere'] = ($where != '' ? $where . ' AND ' : '') . $this->thisConf ['pidTable'] . '.uid NOT IN (' . implode ( ',', $processedUids ) . ')';
		$queryArray = $this->cObj->getQuery ( $this->thisConf ['pidTable'], $this->thisConf ['event_select.'], true );
		
		$result = $GLOBALS ['TYPO3_DB']->exec_SELECT_queryArray ( $queryArray );
		$processedUids = Array ();
		while ( $row = $GLOBALS ['TYPO3_DB']->sql_fetch_assoc ( $result ) ) {
			if (! in_array ( $row ['uid'], $processedUids )) {
				if ($this->thisConf ['enableLocalizationAndVersioning']) {
					if ($GLOBALS ['TSFE']->sys_language_content) {
						$row = $GLOBALS ['TSFE']->sys_page->getRecordOverlay ( $this->thisConf ['pidTable'], $row, $GLOBALS ['TSFE']->sys_language_content, $GLOBALS ['TSFE']->sys_language_contentOL, '' );
					}
					if ($this->versioningEnabled) {
						// get workspaces Overlay
						$GLOBALS ['TSFE']->sys_page->versionOL ( $this->thisConf ['pidTable'], $row );
					}
				}
				$row ['ts_key'] = $this->key;
				$row [$this->thisConf ['startTimeField']] += \TYPO3\CMS\Cal\Utility\Functions::strtotimeOffset ( $row [$this->thisConf ['startTimeField']] );
				if ($this->thisConf ['endTimeField']) {
					$row [$this->thisConf ['endTimeField']] += \TYPO3\CMS\Cal\Utility\Functions::strtotimeOffset ( $row [$this->thisConf ['endTimeField']] );
				}
				$events [gmdate ( 'Ymd', $row [$this->thisConf ['startTimeField']] )] [(gmdate ( 'Hi', $row [$this->thisConf ['startTimeField']] + $this->thisConf ['defaultLength'] ))] [$row ['uid']] = $this->createEvent ( $row, false );
				$processedUids [] = $row ['uid'];
			}
		}
		
		return $events;
	}
	
	/**
	 * Finds a single event.
	 *
	 * @return object event represented by the model.
	 */
	public function find($uid, $pidList) {
		$this->key = $this->controller->piVars ['ts_table'] . '.';
		$service = &$this->getCategoryService ();
		$categories = Array ();
		$service->getCategoryArray ( $pidList, $categories );
		if ($this->conf ['display.'] [$this->controller->piVars ['ts_table'] . '.']) {
			$this->thisConf = $this->conf ['display.'] [$this->controller->piVars ['ts_table'] . '.'];
			$where = $this->cObj->substituteMarkerArrayCached ( $this->thisConf ['findWhere'], Array (
					'###START###' => $start_date,
					'###END###' => $end_date,
					'###UID###' => $this->controller->piVars ['uid'] 
			), Array (), Array () );
			if ($this->thisConf ['enableLocalizationAndVersioning']) {
				$where .= $this->getAdditionalWhereForLocalizationAndVersioning ( $this->thisConf ['pidTable'] );
			}
			// find with
			$this->thisConf ['event_select_with_cat.'] ['pidInList'] = $pidList;
			$this->thisConf ['event_select_with_cat.'] ['andWhere'] = $where;
			$queryArray = $this->cObj->getQuery ( $this->thisConf ['pidTable'], $this->thisConf ['event_select_with_cat.'], true );
			
			$result = $GLOBALS ['TYPO3_DB']->exec_SELECT_queryArray ( $queryArray );
			$event = null;
			$processedUids = Array (0);
			while ( $row = $GLOBALS ['TYPO3_DB']->sql_fetch_assoc ( $result ) ) {
				if (! in_array ( $row ['uid'], $processedUids )) {
					if ($this->thisConf ['enableLocalizationAndVersioning']) {
						if ($GLOBALS ['TSFE']->sys_language_content) {
							$row = $GLOBALS ['TSFE']->sys_page->getRecordOverlay ( $this->thisConf ['pidTable'], $row, $GLOBALS ['TSFE']->sys_language_content, $GLOBALS ['TSFE']->sys_language_contentOL, '' );
						}
						if ($this->versioningEnabled) {
							// get workspaces Overlay
							$GLOBALS ['TSFE']->sys_page->versionOL ( $this->thisConf ['pidTable'], $row );
						}
					}
					$row ['ts_key'] = $this->key;
					$row [$this->thisConf ['startTimeField']] += \TYPO3\CMS\Cal\Utility\Functions::strtotimeOffset ( $row [$this->thisConf ['startTimeField']] );
					if ($this->thisConf ['endTimeField']) {
						$row [$this->thisConf ['endTimeField']] += \TYPO3\CMS\Cal\Utility\Functions::strtotimeOffset ( $row [$this->thisConf ['endTimeField']] );
					}
					$event = $this->createEvent ( $row, false );
					$processedUids [] = $row ['uid'];
				}
				if ($categories [$this->key] [0] [$row ['uid_foreign']]) {
					$event->addCategory ( $categories [$this->key] [0] [$row ['uid_foreign']] );
				}
			}
			if ($event) {
				return $event;
			}
			
			// find without
			$this->thisConf ['event_select.'] ['pidInList'] = $pidList;
			$this->thisConf ['event_select.'] ['andWhere'] = $where . ' AND ' . $this->thisConf ['pidTable'] . '.uid NOT IN (' . implode ( ',', $processedUids ) . ')';
			$queryArray = $this->cObj->getQuery ( $this->thisConf ['pidTable'], $this->thisConf ['event_select.'], true );
			$result = $GLOBALS ['TYPO3_DB']->exec_SELECT_queryArray ( $queryArray );
			$events = Array ();
			$processedUids = Array ();
			while ( $row = $GLOBALS ['TYPO3_DB']->sql_fetch_assoc ( $result ) ) {
				if ($this->thisConf ['enableLocalizationAndVersioning']) {
					if ($GLOBALS ['TSFE']->sys_language_content) {
						$row = $GLOBALS ['TSFE']->sys_page->getRecordOverlay ( $this->thisConf ['pidTable'], $row, $GLOBALS ['TSFE']->sys_language_content, $GLOBALS ['TSFE']->sys_language_contentOL, '' );
					}
					if ($this->versioningEnabled) {
						// get workspaces Overlay
						$GLOBALS ['TSFE']->sys_page->versionOL ( $this->thisConf ['pidTable'], $row );
					}
				}
				$row ['ts_key'] = $this->key;
				$row [$this->thisConf ['startTimeField']] += \TYPO3\CMS\Cal\Utility\Functions::strtotimeOffset ( $row [$this->thisConf ['startTimeField']] );
				if ($this->thisConf ['endTimeField']) {
					$row [$this->thisConf ['endTimeField']] += \TYPO3\CMS\Cal\Utility\Functions::strtotimeOffset ( $row [$this->thisConf ['endTimeField']] );
				}
				$event = $this->createEvent ( $row, false );
			}
			return $event;
		}
		return null;
	}
	
	public function createEvent($row, $isException) {
		return new \TYPO3\CMS\CalTsService\Model\TypoScriptEventModel($row, $isException, $this->getServiceKey (), $this->thisConf );
	}
	
	public function search($pidList = '', $starttime, $endtime, $searchword, $locationIds) {
		$events = Array ();
		if ($this->conf ['display.']) {
			foreach ( $this->conf ['display.'] as $this->key => $conf ) {
				$this->thisConf = $conf;
				$eventsFromService = $this->_search ( $pidList, $starttime, $endtime, $searchword, $locationIds );
				$this->mergeEvents ( $events, $eventsFromService );
			}
		}
		return $events;
	}
	
	private function _search($pidList = '', $starttime, $endtime, $searchword, $locationIds) {
		$events = Array ();
		$where = '';
		if ($searchword != '') {
			$where = $this->searchWhere ( $searchword );
		}
		
		$events = Array ();
		// Find records with categories
		$service = &$this->getCategoryService ();
		$categories = Array ();
		$service->getCategoryArray ( $pidList, $categories );
		
		if ($this->thisConf ['enableLocalizationAndVersioning']) {
			$where .= $this->getAdditionalWhereForLocalizationAndVersioning ( $this->thisConf ['pidTable'] );
		}
		$this->thisConf ['event_select_with_cat.'] ['pidInList'] = $pidList;
		$this->thisConf ['event_select_with_cat.'] ['where'] = '1=1' . $where;
		$queryArray = $this->cObj->getQuery ( $this->thisConf ['pidTable'], $this->thisConf ['event_select_with_cat.'], true );
		$result = $GLOBALS ['TYPO3_DB']->exec_SELECT_queryArray ( $queryArray );
		$processedUids = Array (0);
		
		while ( $row = $GLOBALS ['TYPO3_DB']->sql_fetch_assoc ( $result ) ) {
			if (! in_array ( $row ['uid'], $processedUids )) {
				if ($this->thisConf ['enableLocalizationAndVersioning']) {
					if ($GLOBALS ['TSFE']->sys_language_content) {
						$row = $GLOBALS ['TSFE']->sys_page->getRecordOverlay ( $this->thisConf ['pidTable'], $row, $GLOBALS ['TSFE']->sys_language_content, $GLOBALS ['TSFE']->sys_language_contentOL, '' );
					}
					if ($this->versioningEnabled) {
						// get workspaces Overlay
						$GLOBALS ['TSFE']->sys_page->versionOL ( $this->thisConf ['pidTable'], $row );
					}
				}
				$row ['ts_key'] = $this->key;
				$row [$this->thisConf ['startTimeField']] += \TYPO3\CMS\Cal\Utility\Functions::strtotimeOffset ( $row [$this->thisConf ['startTimeField']] );
				if ($this->thisConf ['endTimeField']) {
					$row [$this->thisConf ['endTimeField']] += \TYPO3\CMS\Cal\Utility\Functions::strtotimeOffset ( $row [$this->thisConf ['endTimeField']] );
				}
				$event = $this->createEvent ( $row, false );
				$events [gmdate ( 'Ymd', $row [$this->thisConf ['startTimeField']] )] [(gmdate ( 'Hi', $row [$this->thisConf ['startTimeField']] + $this->thisConf ['defaultLength'] ))] [$row ['uid']] = $event;
				$processedUids [] = $row ['uid'];
			}
			if ($categories [$this->key] [0] [$row ['uid_foreign']]) {
				$event->addCategory ( $categories [$this->key] [0] [$row ['uid_foreign']] );
			}
		}
		
		// Find records without categories
		$this->thisConf ['event_select.'] ['pidInList'] = $pidList;
		$this->thisConf ['event_select.'] ['where'] = '1=1 ' . $where . ' AND ' . $this->thisConf ['pidTable'] . '.uid NOT IN (' . implode ( ',', $processedUids ) . ')';
		$queryArray = $this->cObj->getQuery ( $this->thisConf ['pidTable'], $this->thisConf ['event_select.'], true );
		
		$result = $GLOBALS ['TYPO3_DB']->exec_SELECT_queryArray ( $queryArray );
		$processedUids = Array ();
		while ( $row = $GLOBALS ['TYPO3_DB']->sql_fetch_assoc ( $result ) ) {
			if (! in_array ( $row ['uid'], $processedUids )) {
				if ($this->thisConf ['enableLocalizationAndVersioning']) {
					if ($GLOBALS ['TSFE']->sys_language_content) {
						$row = $GLOBALS ['TSFE']->sys_page->getRecordOverlay ( $this->thisConf ['pidTable'], $row, $GLOBALS ['TSFE']->sys_language_content, $GLOBALS ['TSFE']->sys_language_contentOL, '' );
					}
					if ($this->versioningEnabled) {
						// get workspaces Overlay
						$GLOBALS ['TSFE']->sys_page->versionOL ( $this->thisConf ['pidTable'], $row );
					}
				}
				$row ['ts_key'] = $this->key;
				$row [$this->thisConf ['startTimeField']] += \TYPO3\CMS\Cal\Utility\Functions::strtotimeOffset ( $row [$this->thisConf ['startTimeField']] );
				if ($this->thisConf ['endTimeField']) {
					$row [$this->thisConf ['endTimeField']] += \TYPO3\CMS\Cal\Utility\Functions::strtotimeOffset ( $row [$this->thisConf ['endTimeField']] );
				}
				$events [gmdate ( 'Ymd', $row [$this->thisConf ['startTimeField']] )] [(gmdate ( 'Hi', $row [$this->thisConf ['startTimeField']] + $this->thisConf ['defaultLength'] ))] [$row ['uid']] = $this->createEvent ( $row, false );
				$processedUids [] = $row ['uid'];
			}
		}
		
		return $events;
	}
	
	/**
	 * Generates a search where clause.
	 *
	 * @param string $sw:        	
	 * @return string
	 */
	public function searchWhere($sw) {
		return $this->cObj->searchWhere ( $sw, $this->thisConf ['search.'] ['searchEventFieldList'], $this->thisConf ['table'] );
	}
	
	public function getCategoryService() {
		if (is_object ( $this->categoryService )) {
			return $this->categoryService;
		}
		$this->categoryService = & GeneralUtility::makeInstance ( 'TYPO3\\CMS\\CalTsService\\Service\\CategoryService' );
		return $this->categoryService;
	}
}

?>