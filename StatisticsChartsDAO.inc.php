<?php

/**
 * @file plugins/generic/statistics/StatisticsChartsDAO.inc.php
 *
 * Copyright (c) 2016 Fran Máñez - Universitat Politècnica de Catalunya (UPC)
 * fran.upc@gmail.com
 *
 * @class StatisticsChartsDAO
 *
 */

import('classes.statistics.MetricsDAO');

class StatisticsChartsDAO extends MetricsDAO {
	
	function getMetricsMonthByType($journalId, $assoc_type, $year){
		$result =& $this->retrieve(
			'SELECT month, SUM(metric) FROM metrics WHERE context_id = ? AND assoc_type = ? AND SUBSTR(month,1,4) = ? GROUP BY month order by month ASC',
			array((int) $journalId, (int) $assoc_type, $year)
		);
		
		$returner = null;
		if ($result->RecordCount() != 0) {
			$returner = $result->GetArray();
		}

		$result->Close();
		unset($result);

		return $returner;
	}
	
	function getMetricsYearByType($journalId, $assoc_type, $year){
		$yearInit = $year-5;
		$yearEnd = $year;
		$result =& $this->retrieve(
			'SELECT SUBSTR(month,1,4), SUM(metric) FROM metrics WHERE context_id = ? AND assoc_type = ? AND SUBSTR(month,1,4)>= ? AND SUBSTR(month,1,4)<= ? GROUP BY SUBSTR(month,1,4) order by SUBSTR(month,1,4) ASC',
			array((int) $journalId, (int) $assoc_type, $yearInit, $yearEnd)
		);
		
		$returner = null;
		if ($result->RecordCount() != 0) {
			$returner = $result->GetArray();
		}

		$result->Close();
		unset($result);

		return $returner;
	}
	
	function getMetricsByCountryType($journalId, $assoc_type, $year){
		$result =& $this->retrieve(
			'SELECT country_id, SUM(metric) FROM metrics WHERE context_id = ? AND assoc_type = ? AND SUBSTR(month,1,4) = ? GROUP BY country_id order by SUM(metric) ASC LIMIT 20;',
			array((int) $journalId, (int) $assoc_type, $year)
		);
		
		$returner = null;
		if ($result->RecordCount() != 0) {
			$returner = $result->GetArray();
		}

		$result->Close();
		unset($result);

		return $returner;
	}
	
	function getMetricsMostPopularByType($journalId, $assoc_type, $year, $primaryLocale){
		$result =& $this->retrieve(
			'SELECT aset.setting_value, SUM(m.metric) FROM metrics as m '. 
			'INNER JOIN article_settings AS aset ON m.submission_id = aset.article_id '.
			'WHERE m.context_id = ? '.
			'AND m.assoc_type = ? '.
			'AND SUBSTR(month,1,4) = ? '.
			'AND aset.setting_name = "title" '.
			'AND aset.locale = ? '.
			'GROUP BY m.assoc_id order by SUM(m.metric) DESC LIMIT 20;',
			array((int) $journalId, (int) $assoc_type, $year, $primaryLocale)
		);
		
		$returner = null;
		if ($result->RecordCount() != 0) {
			$returner = $result->GetArray();
		}

		$result->Close();
		unset($result);

		return $returner;
	}
	
	function getMetricsIssues($journalId, $year, $primaryLocale){
		$result =& $this->retrieve(
			'SELECT i.volume, i.number, i.year, iset.setting_value, SUM(m.metric) FROM metrics as m '. 
			'inner join issues as i on m.issue_id = i.issue_id '.
			'inner join issue_settings as iset on i.issue_id= iset.issue_id '.
			'WHERE m.context_id = ? '.
			'AND m.assoc_type = 259 '.
			'AND SUBSTR(month,1,4) = ? '.
			'AND iset.setting_name = "title" '.
			'AND iset.locale = ? '.
			'AND i.published = 1 '.
			'GROUP BY m.issue_id '.
			'ORDER BY SUM(m.metric) DESC LIMIT 20;',
			array((int) $journalId, $year, $primaryLocale)
		);
		
		$returner = null;
		if ($result->RecordCount() != 0) {
			$returner = $result->GetArray();
		}

		$result->Close();
		unset($result);

		return $returner;
	}
}

?>
