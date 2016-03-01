<?php

namespace TYPO3\CMS\CalTsService\Model;

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
 * A concrete model for the calendar.
 *
 * @author Mario Matzulla <mario(at)matzullas.de>
 */
class TypoScriptEventModel extends \TYPO3\CMS\Cal\Model\EventModel {
	var $thisConf = Array ();
	
	/**
	 *
	 * @param unknown $row        	
	 * @param unknown $isException        	
	 * @param unknown $serviceKey        	
	 * @param unknown $conf        	
	 */
	public function __construct($row, $isException, $serviceKey, $conf = Array()) {
		parent::__construct ( $row, false, $serviceKey );
		$this->thisConf = $conf;
		
		// $this->conf is a linked variable.
		// Let's duplicate it
		$tempConf = $this->conf;
		// Unset the link
		unset ( $this->conf );
		// And refill it again :)
		$this->conf = $tempConf;
		
		$this->conf ['view.'] [$this->conf ['view'] . '.'] ['event.'] = array_merge ( (array) $this->conf ['view.'] [$this->conf ['view'] . '.'] ['event.'], ( array ) $conf [$this->conf ['view'] . '.'] ['event.'] );
		$this->isException = $isException;
		$this->setType ( 'tx_cal_ts_service' );
		$this->createEvent ( $row, $isException );
	}
	
	/**
	 *
	 * @param unknown $row        	
	 */
	public function createEvent($row) {
		$this->setType ( $this->serviceKey );
		$this->row = $row;
		$this->setUid ( $row ['uid'] );
		$this->setAllday ( $row ['allday'] );
		$this->setExtUrl ( $row ['ext_url'] );
		
		$start = new \TYPO3\CMS\Cal\Model\CalDate ( gmdate ( 'Ymd', $row [$this->thisConf ['startTimeField']] ) );
		$start->setTZbyId ( 'UTC' );
		$start->setMinute ( gmdate ( 'i', $row [$this->thisConf ['startTimeField']] ) );
		$start->setHour ( gmdate ( 'H', $row [$this->thisConf ['startTimeField']] ) );
		
		if (! $row [$this->thisConf ['endTimeField']] && $this->thisConf ['defaultLength'] && ! $this->isAllday ()) {
			$end = new \TYPO3\CMS\Cal\Model\CalDate ();
			$end->copy ( $start );
			$end->addSeconds ( $this->thisConf ['defaultLength'] * 60 );
		} else {
			$end = new \TYPO3\CMS\Cal\Model\CalDate ( gmdate ( 'Ymd', $row [$this->thisConf ['endTimeField']] ) );
			$end->setTZbyId ( 'UTC' );
			$end->setMinute ( gmdate ( 'i', $row [$this->thisConf ['endTimeField']] ) );
			$end->setHour ( gmdate ( 'H', $row [$this->thisConf ['endTimeField']] ) );
		}
		
		$this->setStart ( $start );
		$this->setEnd ( $end );
		
		$this->setFreq ( $row ['freq'] );
		$this->setByDay ( $row ['byday'] );
		$this->setByMonthDay ( $row ['bymonthday'] );
		$this->setByMonth ( $row ['bymonth'] );
		if (isset ( $row ['until'] )) {
			$until = new \TYPO3\CMS\Cal\Model\CalDate ( $row ['until'] );
			$until->setTZbyId ( 'UTC' );
			$this->setUntil ( $until );
		}
		foreach ( ( array ) $this->thisConf ['fieldMapping.'] as $field => $value ) {
			switch ($field) {
				case 'image' :
					$this->setImage ( GeneralUtility::trimExplode ( ',', $row [$this->thisConf ['fieldMapping.'] ['image']] ) );
					break;
				default :
					$funcName = 'set' . ucwords ( strtolower ( $field ) );
					if (method_exists ( $this, $funcName )) {
						$this->$funcName ( $row [$this->thisConf ['fieldMapping.'] [$field]] );
					}
			}
		}
		
		$this->externalPlugin = $this->thisConf ['externalPlugin'];
	}
	
	/**
	 *
	 * @param unknown $template        	
	 * @param unknown $rems        	
	 * @param unknown $sims        	
	 * @param unknown $view        	
	 */
	public function getCategoryHeaderStyle(&$template, &$rems, &$sims, $view) {
		$sims ['###HEADERSTYLE###'] = $this->thisConf ['headerStyle'];
	}
	
	/**
	 *
	 * @param unknown $template        	
	 * @param unknown $rems        	
	 * @param unknown $sims        	
	 * @param unknown $view        	
	 */
	public function getCategoryBodyStyle(&$template, &$rems, &$sims, $view) {
		$sims ['###BODYSTYLE###'] = $this->thisConf ['bodyStyle'];
	}
	
	/**
	 * Returns the headerstyle name
	 */
	function getHeaderStyle() {
		return $this->thisConf ['headerStyle'];
	}
	
	/**
	 * Returns the bodystyle name
	 */
	public function getBodyStyle() {
		return $this->thisConf ['bodyStyle'];
	}
	
	/**
	 *
	 * @return string
	 */
	public function renderEvent() {
		return $this->fillTemplate ( '###TEMPLATE_TS_EVENT###' );
	}
	
	/**
	 */
	public function renderTomorrowsEvent() {
		$this->isTomorrow = true;
		$this->renderEventFor ( 'TOMORROW' );
	}
	
	/**
	 *
	 * @param unknown $viewType        	
	 * @return string
	 */
	public function renderEventFor($viewType) {
		if ($this->row ['isFreeAndBusyEvent'] == 1) {
			$viewType .= '_FNB';
		}
		if (substr ( $viewType, - 6 ) != 'ALLDAY' && ($this->isAllday () || $this->getStart ()->format ( '%Y%m%d' ) != $this->getEnd ()->format ( '%Y%m%d' ))) {
			$subpartSuffix .= 'ALLDAY';
		}
		return $this->fillTemplate ( '###TEMPLATE_TS_EVENT_' . strtoupper ( $viewType ) . '###' );
	}
	
	/**
	 *
	 * @param unknown $subpartMarker        	
	 * @return string
	 */
	public function fillTemplate($subpartMarker) {
		// $this->controller->piVars['ts_table'] = $this->thisConf['table'];
		$cObj = &$this->controller->cObj;
		$page = $cObj->fileResource ( $this->thisConf ['template'] );
		if ($page == '') {
			return '<h3>calendar: no template file found:</h3>' . $this->thisConf ['template'];
		}
		$page = $cObj->getSubpart ( $page, $subpartMarker );
		$rems = Array ();
		$sims = Array ();
		$wrapped = Array ();
		$this->getMarker ( $page, $sims, $rems, $wrapped );
		return $this->finish ( $cObj->substituteMarkerArrayCached ( $page, $sims, $rems, $wrapped ) );
	}
	
	/**
	 *
	 * @return unknown
	 */
	public function getSubheader() {
		return $this->subheader;
	}
	
	/**
	 *
	 * @param unknown $s        	
	 */
	public function setSubheader($s) {
		$this->subheader = $s;
	}
	
	/**
	 *
	 * @return \TYPO3\CMS\Cal\Model\CalDate
	 */
	public function getUntil() {
		if (! isset ( $this->until ) || $this->until == 0) {
			return new \TYPO3\CMS\Cal\Model\CalDate ( '00000101' );
		}
		return $this->until;
	}
	
	/**
	 *
	 * @return unknown
	 */
	public function getCategory() {
		return $this->category;
	}
	
	/**
	 *
	 * @param unknown $cat        	
	 */
	public function setCategory($cat) {
		$this->category = $cat;
	}
	
	/**
	 *
	 * @param unknown $template        	
	 * @param unknown $sims        	
	 * @param unknown $rems        	
	 * @param unknown $wrapped        	
	 * @param unknown $view        	
	 */
	public function getImageMarker(& $template, & $sims, & $rems, & $wrapped, $view) {
		$tempConfig = $this->conf ['view.'] [$view . '.'] [$this->objectType . '.'] ['image.'];
		$tempType = $this->conf ['view.'] [$view . '.'] [$this->objectType . '.'] ['image'];
		$this->conf ['view.'] [$view . '.'] [$this->objectType . '.'] ['image.'] = $this->thisConf [$this->conf ['view'] . '.'] [$this->objectType . '.'] ['image.'];
		$this->conf ['view.'] [$view . '.'] [$this->objectType . '.'] ['image'] = $this->thisConf [$this->conf ['view'] . '.'] [$this->objectType . '.'] ['image'];
		parent::getImageMarker ( $template, $sims, $rems, $wrapped, $view );
		$this->conf ['view.'] [$view . '.'] [$this->objectType . '.'] ['image.'] = $tempConfig;
		$this->conf ['view.'] [$view . '.'] [$this->objectType . '.'] ['image'] = $tempType;
	}
	
	/**
	 * Returns the Link to the external plugin
	 */
	public function getExternalPluginEventLink() {
		$cObj = &$this->controller->cObj;
		if ($this->ext_url) {
			return $this->controller->pi_linkTP ( '$5&xs2', Array (), 0, $this->ext_url );
		}
		$params = $cObj->stdWrap ( $this->thisConf ['externalPlugin.'] ['additionalParams'], $this->thisConf ['externalPlugin.'] ['additionalParams.'] );
		$rems = Array ();
		$sims = Array ();
		$wrapped = Array ();
		$this->getMarker ( $params, $rems, $sims, $wrapped );
		$params = $cObj->substituteMarkerArrayCached ( $params, $sims, $rems, $wrapped );
		$paramArray = GeneralUtility::trimExplode ( '$5&xs2', $params, 1 );
		$urlParams = Array ();
		foreach ( $paramArray as $parameter ) {
			$valArray = GeneralUtility::trimExplode ( '=', $parameter, 1 );
			$urlParams [$valArray [0]] = $valArray [1];
		}
		
		return $this->controller->pi_linkTP ( '$5&xs2', $urlParams, $this->conf ['cache'], $this->thisConf ['externalPlugin.'] ['singleViewPid'] );
	}
	
	/**
	 *
	 * @param unknown $currentParams        	
	 */
	public function addAdditionalSingleViewUrlParams(&$currentParams) {
		$currentParams ['ts_table'] = str_replace ( '.', '', $this->row ['ts_key'] );
		$currentParams ['type'] = $this->getType ();
	}
	
	/**
	 *
	 * @return \TYPO3\CMS\CalTsService\Model\TypoScriptEventModel
	 */
	public function cloneEvent() {
		$event = new \TYPO3\CMS\CalTsService\Model\TypoScriptEventModel ( $this->getValuesAsArray (), $this->isException, $this->getType (), $this->thisConf );
		$event->setIsClone ( true );
		return $event;
	}
}

?>