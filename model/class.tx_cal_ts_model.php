<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004 
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(t3lib_extMgm::extPath('cal').'model/class.tx_cal_phpicalendar_model.php');

/**
 * A concrete model for the calendar.
 *
 * @author Mario Matzulla <mario(at)matzullas.de>
 */
class tx_cal_ts_model extends tx_cal_phpicalendar_model {
	
	var $thisConf;
	
	function tx_cal_ts_model($row, $isException, $serviceKey, $conf=Array()){
		$this->tx_cal_phpicalendar_model($row, false, $serviceKey);
		$this->controller = &tx_cal_registry::Registry('basic','controller');
		$this->thisConf = $conf;
		$this->conf['view.'][$this->conf['view'].'.']['event.'] = array_merge($this->conf['view.'][$this->conf['view'].'.']['event.'],(array)$conf[$this->conf['view'].'.']['event.']);
		$this->isException = $isException;	
		$this->createEvent($row, $isException);
		$this->setType('tx_cal_ts_service');
	}
	
	# @override
	function tx_cal_base_model(&$serviceKey){
		$this->conf = tx_cal_registry::Registry('basic','conf');
		$this->serviceKey = &$serviceKey;
	}
	
	function createEvent($row){
		$this->setType($this->serviceKey);
		$this->row = $row;
		$this->setUid($row['uid']);
		$this->setAllday($row['allday']);
		$this->setExtUrl($row['ext_url']);

		$start = new tx_cal_date(gmdate('Ymd',$row[$this->thisConf['startTimeField']]));
		$start->setTZbyId('UTC');
		$start->setMinute(gmdate('i',$row[$this->thisConf['startTimeField']]));
		$start->setHour(gmdate('H',$row[$this->thisConf['startTimeField']]));
		
		if(!$row[$this->thisConf['endTimeField']] && $this->thisConf['defaultLength'] && !$this->isAllday()){
			$end = new tx_cal_date();
			$end->copy($start);
			$end->addSeconds($this->thisConf['defaultLength']*60);
		}else{
			$end = new tx_cal_date(gmdate('Ymd',$row[$this->thisConf['endTimeField']]));
			$end->setTZbyId('UTC');
			$end->setMinute(gmdate('i',$row[$this->thisConf['endTimeField']]));
			$end->setHour(gmdate('H',$row[$this->thisConf['endTimeField']]));
		}
		
		$this->setStart($start);
		$this->setEnd($end);
		
		$this->setFreq($row['freq']);
		$this->setByDay($row['byday']);
		$this->setByMonthDay($row['bymonthday']);
		$this->setByMonth($row['bymonth']);
		if(isset($row['until'])){
			$until = new tx_cal_date($row['until']);
			$until->setTZbyId('UTC');
			$this->setUntil($until);
		}	
		foreach((Array)$this->thisConf['fieldMapping.'] as $field => $value){
			switch ($field){
				case 'image':
					$this->setImage(t3lib_div::trimExplode(',',$row[$this->thisConf['fieldMapping.']['image']]));
					break;
				default:
					$funcName = 'set'.ucwords(strtolower($field));
					if(method_exists($this,$funcName)) {
						$this->$funcName($row[$this->thisConf['fieldMapping.'][$field]]);
					}
			}
		}
		
		$this->externalPlugin = $this->thisConf['externalPlugin'];
	}
	
	function getCategoryHeaderStyle(&$template, &$rems, &$sims, $view){
		$sims['###HEADERSTYLE###'] = $this->thisConf['headerStyle'];
	}
	
	function getCategoryBodyStyle(&$template, &$rems, &$sims, $view){
		$sims['###BODYSTYLE###'] = $this->thisConf['bodyStyle'];
	}
	
	/**
	  * Returns the headerstyle name
	  */
	 function getHeaderStyle(){
	 	return $this->thisConf['headerStyle'];
	 }
	 
	 /**
	  * Returns the bodystyle name
	  */
	 function getBodyStyle(){
	 	return $this->thisConf['bodyStyle'];
	 }

	function renderEventForDay() {
		return $this->fillTemplate('###TEMPLATE_TS_EVENT_DAY###');
	}

	function renderEventForWeek() {
		return $this->fillTemplate('###TEMPLATE_TS_EVENT_WEEK###');
	}

	function renderEventForAllDay() {
		return $this->fillTemplate('###TEMPLATE_TS_EVENT_ALLDAY###');
	}

	function renderEventForMonth() {
		if($this->isAllday()){
			return $this->renderEventFor('MONTH_ALLDAY');
		}
		return $this->renderEventFor('MONTH');
	}
	
	function renderEventForYear() {
		return $this->fillTemplate('###TEMPLATE_TS_EVENT_YEAR###');
	}

	function renderEvent() {
		return $this->fillTemplate('###TEMPLATE_TS_EVENT###');
	}
	
	function renderTomorrowsEvent() {
		$this->isTomorrow = true;
		return $this->fillTemplate('###TEMPLATE_TS_EVENT_TOMORROW###');
	}
	
	function renderEventFor($viewType){
		return $this->fillTemplate('###TEMPLATE_TS_EVENT_'.strtoupper($viewType).'###');
	}
	
	function fillTemplate($subpartMarker){
#		$this->controller->piVars['ts_table'] = $this->thisConf['table'];
		$cObj = &$this->controller->cObj;
		$page = $cObj->fileResource($this->thisConf['template']);
		if ($page == '') {
			return '<h3>calendar: no template file found:</h3>' . $this->thisConf['template'];
		}
		$page = $cObj->getSubpart($page,$subpartMarker);
		$rems = array ();
		$sims = array ();
		$wrapped = array();
		$this->getMarker($page, $sims, $rems, $wrapped);
#		unset($this->controller->piVars['ts_table']);
		$return = $this->finish($cObj->substituteMarkerArrayCached($page, $sims, $rems, $wrapped));
		return $return;
	}
	
	function getSubheader(){
		return $this->subheader;
	}
	
	function setSubheader($s){
		$this->subheader = $s;
	}
	
	function getUntil(){
		if(!isset($this->until) || $this->until==0){
			return new tx_cal_date('00000101');
		}
		return $this->until;	
	}
	
	function getCategory(){
		return $this->category;
	}
	
	function setCategory($cat){
		$this->category = $cat;
	}
	
	function getImageMarker(& $template, & $sims, & $rems, & $wrapped, $view){
		$tempConfig = $this->conf['view.'][$view.'.'][$this->objectType.'.']['image.'];
		$this->conf['view.'][$view.'.'][$this->objectType.'.']['image.'] = $this->thisConf[$this->conf['view'].'.'][$this->objectType.'.']['image.'];
		parent::getImageMarker($template, $sims, $rems, $wrapped, $view);
		$this->conf['view.'][$view.'.'][$this->objectType.'.']['image.'] = $tempConfig;
	}
	
	/**
	 * Returns the Link to the external plugin
	 */
	function getExternalPluginEventLink() {
		$cObj = &$this->controller->cObj;
		if ($this->ext_url) {
			return $this->controller->pi_linkTP(
				'|',
				array(),
				0,
				$this->ext_url
			);
		}
		$params = $cObj->stdWrap($this->thisConf['externalPlugin.']['additionalParams'],$this->thisConf['externalPlugin.']['additionalParams.']);
		$rems = array ();
		$sims = array ();
		$wrapped = array();
		$this->getMarker($params, $rems, $sims, $wrapped);
		$params = $cObj->substituteMarkerArrayCached($params, $sims, $rems, $wrapped);
		$paramArray = t3lib_div::trimExplode('|',$params,1);
		$urlParams = array();
		foreach($paramArray as $parameter){
			$valArray = t3lib_div::trimExplode('=',$parameter,1);
			$urlParams[$valArray[0]]=$valArray[1];
		}

		return $this->controller->pi_linkTP(
			'|',
			$urlParams,
			$this->conf['cache'],
			$this->thisConf['externalPlugin.']['singleViewPid']
		);
		
	}
	
	function addAdditionalSingleViewUrlParams(&$currentParams){
		$currentParams['ts_table'] = str_replace('.','',$this->row['ts_key']);
	}
	
	function cloneEvent() {
		$event = t3lib_div :: makeInstance(get_class($this), $this->getValuesAsArray(), $this->isException, $this->getType(), $this->thisConf);
		$event->setIsClone(true);
		return $event;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cal_ts_service/model/class.tx_cal_ts_model.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cal_ts_service/model/class.tx_cal_ts_model.php']);
}
?>