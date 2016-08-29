<?php

/**
 * @file plugins/generic/statistics/StatisticsHandler.inc.php
 *
 * Copyright (c) 2016 Fran Máñez - Universitat Politècnica de Catalunya (UPC)
 * fran.upc@gmail.com
 *
 * @class StatisticsHandler
 *
 */

import('classes.handler.Handler');

define('STATISTICS_METRICS_ASSOCTYPE_HOME', 256);
define('STATISTICS_METRICS_ASSOCTYPE_TABLECONTENTS', 259);
define('STATISTICS_METRICS_ASSOCTYPE_ABSTRACT', 257);
define('STATISTICS_METRICS_ASSOCTYPE_DOWNLOAD', 260);

class StatisticsHandler extends Handler {

	/**
	 * Constructor
	 **/
	function StatisticsHandler() {
		parent::Handler();
	}
	
	/**
	 * Display the main log analyzer page.
	 */
	function index() {
		//this plugin depends on 'Counter Plugin'
		//$plugins =& PluginRegistry::getPlugins();
		
		$this->validate();
		$this->setupTemplate();
		$plugin =& $this->plugin;

		$templateManager =& TemplateManager::getManager();
		
		// library highcharts jquery
		$templateManager->addJavaScript('plugins/generic/statistics/js/highcharts/highcharts.js');
		$templateManager->addJavaScript('plugins/generic/statistics/js/highcharts/highcharts-3d.js');
		$templateManager->addJavaScript('plugins/generic/statistics/js/highcharts/themes/grid-light.js');
		$templateManager->addJavaScript('plugins/generic/statistics/js/highcharts/modules/exporting.js');
		
		$templateManager->addJavaScript('plugins/generic/statistics/js/jquery-1.11.3.min.js');
		$templateManager->addJavaScript('plugins/generic/statistics/js/bootstrap.min.js');
		$templateManager->addJavaScript('plugins/generic/statistics/js/bootstrap-switch.min.js');
		
		$templateManager->display($plugin->getTemplatePath() . 'index.tpl');
	}
	
	/**
	 * Get statistics (download and abstract) by month year from table METRICS
	 */
	function getStatisticsByMonth(){
//		$this->validate();
//		$this->setupTemplate(true);
//		$plugin =& $this->plugin;

		$journal =& Request::getJournal();

		$year = Request::getUserVar('year');
		if (empty($year)) $year = date('Y');
		
		//statistics report
		$statisticsChartsDAO =& DAORegistry::getDAO('StatisticsChartsDAO');
			
		//statistics abstract
		$result = $statisticsChartsDAO->getMetricsMonthByType($journal->getId(), STATISTICS_METRICS_ASSOCTYPE_ABSTRACT, $year);
		StatisticsHandler::dataForMonth($cols, $result);
		$obj[0] = new stdClass();
		$obj[0]->name = AppLocale::Translate('plugins.generic.statistics.viewAbstracts');
		$obj[0]->values = $cols;
		unset($cols);
		
		//statistics download
		$result = $statisticsChartsDAO->getMetricsMonthByType($journal->getId(), STATISTICS_METRICS_ASSOCTYPE_DOWNLOAD, $year);
		StatisticsHandler::dataForMonth($cols, $result);
		$obj[1] = new stdClass();
		$obj[1]->name = AppLocale::Translate('plugins.generic.statistics.viewDownloads');
		$obj[1]->values = $cols;
		unset($cols);

		echo json_encode($obj);
	}
	
	
	/**
	 * Get statistics (download and abstract) by year from table METRICS
	 */
	function getStatisticsByYear(){

		$journal =& Request::getJournal();

		$year = Request::getUserVar('year');
		if (empty($year)) $year = date('Y');
		
		$statisticsChartsDAO =& DAORegistry::getDAO('StatisticsChartsDAO');
			
		//statistics abstract
		$result = $statisticsChartsDAO->getMetricsYearByType($journal->getId(), STATISTICS_METRICS_ASSOCTYPE_ABSTRACT, $year);
		StatisticsHandler::dataForYear($cols, $result, $year);
		$obj[0] = new stdClass();
		$obj[0]->name = AppLocale::Translate('plugins.generic.statistics.viewAbstracts');
		$obj[0]->values = $cols;
		unset($cols);
		
		//statistics download
		$result = $statisticsChartsDAO->getMetricsYearByType($journal->getId(), STATISTICS_METRICS_ASSOCTYPE_DOWNLOAD, $year);
		StatisticsHandler::dataForYear($cols, $result, $year);
		$obj[1] = new stdClass();
		$obj[1]->name = AppLocale::Translate('plugins.generic.statistics.viewDownloads');
		$obj[1]->values = $cols;
		unset($cols);
		
		echo json_encode($obj);
		
	}
	
	/**
	 * Get statistics (abstract) by country from table METRICS
	 */
	function getStatisticsByCountryAbstract(){
		
		$journal =& Request::getJournal();

		$year = Request::getUserVar('year');
		if (empty($year)) $year = date('Y');
		
		$statisticsChartsDAO =& DAORegistry::getDAO('StatisticsChartsDAO');
			
		$result = $statisticsChartsDAO->getMetricsByCountryType($journal->getId(), STATISTICS_METRICS_ASSOCTYPE_ABSTRACT, $year);
		
		$i = 0;
		$obj = array();
		
		if($result!=null){
			foreach ($result as $record) {
				$obj[$i] = new stdClass();
				$obj[$i]->country = $record[0] == null ? "Others" : $record[0];
				$obj[$i]->count = $record[1];
				$i++;
			}
		}
		
		
		echo json_encode($obj);
		
	}
	
	/**
	 * Get statistics (download) by country from table METRICS
	 */
	function getStatisticsByCountryDownload(){

		$journal =& Request::getJournal();

		$year = Request::getUserVar('year');
		if (empty($year)) $year = date('Y');
		
		$statisticsChartsDAO =& DAORegistry::getDAO('StatisticsChartsDAO');
			
		$result = $statisticsChartsDAO->getMetricsByCountryType($journal->getId(), STATISTICS_METRICS_ASSOCTYPE_DOWNLOAD, $year);
		$i = 0;
		$obj = array();
		
		if($result!=null){
			foreach ($result as $record) {
				$obj[$i] = new stdClass();
				$obj[$i]->country = $record[0] == null ? "Others" : $record[0];
				$obj[$i]->count = $record[1];
				$i++;
			}
		}
		
		echo json_encode($obj);
		
	}
	
	/**
	 * Get statistics (download or abstract, request parameter) most popular articles from table METRICS
	 */
	function getStatisticsMostPopularDownload(){
	
		$journal =& Request::getJournal();
		$primaryLocale = $journal->getPrimaryLocale();

		$year = Request::getUserVar('year');
		if (empty($year)) $year = date('Y');
		
		$type = Request::getUserVar('type');
		
		$statisticsChartsDAO =& DAORegistry::getDAO('StatisticsChartsDAO');
			
		$result = $statisticsChartsDAO->getMetricsMostPopularByType($journal->getId(), $type, $year, $primaryLocale);
		
		$i = 0;
		$obj = array();
		if($result!=null){
			foreach ($result as $record) {
				$object = (object) array('article' => $record[0], 'count' => $record[1]);
				$obj[] = $object;
				$i++;
			}
		}
		
		echo json_encode($obj);
		
	}
	
	/**
	 * Get statistics (download) most popular articles from table METRICS
	 */
	function getStatisticsIssues(){
		
		$journal =& Request::getJournal();
		$primaryLocale = $journal->getPrimaryLocale();

		$year = Request::getUserVar('year');
		if (empty($year)) $year = date('Y');
		
		//statistics report
		$statisticsChartsDAO =& DAORegistry::getDAO('StatisticsChartsDAO');
			
		//statistics abstract
		$result = $statisticsChartsDAO->getMetricsIssues($journal->getId(), $year, $primaryLocale);
		
		$i = 0;
		$obj = array();
		if($result!=null){
			foreach ($result as $record) {
				$object = (object) array('volume' => $record[0], 'number' => $record[1], 'year' => $record[2], 'name' => $record[3], 'count' => $record[4]);
				$obj[] = $object;
				$i++;
			}
		}
		
		echo json_encode($obj);
		
	}
	
	/**
	 * Validate that user has site admin privileges or journal manager priveleges.
	 * Redirects to the user index page if not properly authenticated.
	 * @param $canRedirect boolean Whether or not to redirect if the user cannot be validated; if not, the script simply terminates.
	 */
	function validate($canRedirect = true) {
		parent::validate();
		$plugin =& Registry::get('plugin');
		$this->plugin =& $plugin;
		return true;
	}

	
	/**
	 * ************************************************************
	 * 					PARSER DATA FUNCTIONS
	 * ************************************************************
	 */
	
	private function dataForMonth(&$cols, $entries) {
		if($entries == null) return;
		$currTotal = 0;
		for ($i = 1; $i <= 12; $i++) {
			$currTotal = 0;
			foreach ($entries as $entry) {
				$month = substr($entry[0], -2, 2);
				if ($i==$month) {
					$currTotal += $entry[1];
				}
			}
			$cols[]=$currTotal;
		}
	}
	
	private function dataForYear(&$cols, $entries, $year) {
		if($entries == null) return;
		$currTotal = 0;
		for ($i = $year-5; $i <= $year; $i++) {
			$currTotal = 0;
			foreach ($entries as $entry) {
				if ($i==$entry[0]) {
					$currTotal += $entry[1];
				}
			}
			$cols[]=$currTotal;
		}
	}
}

?>
