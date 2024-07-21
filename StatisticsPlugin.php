<?php

/**
 * @file plugins/generic/statistics/StatisticsPlugin.inc.php
 *
 * Copyright (c) 2016 Fran Máñez - Universitat Politècnica de Catalunya (UPC)
 * fran.upc@gmail.com
 *
 * @class StatisticsPlugin
 *
 */

namespace APP\plugins\generic\StatisticsPlugin;

use APP\core\Application;
use APP\core\Request;
use PKP\core\JSONMessage;
use PKP\core\PKPString;
use PKP\linkAction\LinkAction;
use PKP\linkAction\request\AjaxModal;
use PKP\plugins\GenericPlugin;
use PKP\plugins\Hook;
use APP\template\TemplateManager;

class StatisticsPlugin extends GenericPlugin
{
	/**
	 * Called as a plugin is registered to the registry
	 * @param $category String Name of category plugin was registered to
	 * @return boolean True iff plugin initialized successfully; if false,
	 * 	the plugin will not be registered.
	 */
	function register($category, $path, $mainContextId = NULL)
	{
		$success = parent::register($category, $path);
		if ($success && $this->getEnabled()) {

			Hook::add('LoadHandler', array(&$this, 'handleRequest'));
			// Hook::add ('Templates::Common::Header::Navbar::CurrentJournal', array(&$this, 'displayHeaderLink'));


			// $this->import('StatisticsChartsDAO');
			// $statisticsChartsDao = new StatisticsChartsDAO();
			// DAORegistry::registerDAO('StatisticsChartsDAO', $statisticsChartsDao);

		}
		return $success;
	}

	function getName()
	{
		return 'StatisticsPlugin';
	}

	function getDisplayName()
	{
		return __('plugins.generic.statistics.name');
	}

	function getDescription()
	{
		return __('plugins.generic.statistics.description');
	}

	// function displayHeaderLink($hookName, $params) {
	// 	$journal =& Application::get()
	// 	->getRequest()
	// 	->getContext();

	// 	if (!$journal) return false;

	// 	if ($this->getEnabled()) {
	// 		$smarty =& $params[1];
	// 		$output =& $params[2];
	// 		$templateMgr = TemplateManager::getManager();
	// 		$output .= '<li><a href="' . $templateMgr->smartyUrl(array('page'=>'statistics'), $smarty) . '" target="_parent">' . $templateMgr->smartyTranslate(array('key'=>'plugins.generic.statistics.name'), $smarty) . '</a></li>';
	// 	}
	// 	return false;
	// }


	function handleRequest($hookName, $args)
	{
		$page = &$args[0];
		$op = &$args[1];
		$sourceFile = &$args[2];
		$handler = &$args[3];

		// If the request is for the log analyzer itself, handle it.
		if ($this->getEnabled() && $page === 'statistics') {
			$handler = new StatisticsHandler($this);
			// Registry::set('plugin', $this);
			// define('HANDLER_CLASS', 'StatisticsHandler');
			return true;
		}
		return false;
	}

	function isSitePlugin()
	{
		return true;
	}

	// function getManagementVerbs() {
	// 	$verbs = array();

	// 	return parent::getManagementVerbs ($verbs);
	// }

	/*
 	 * Execute a management verb on this plugin
 	 * @param $verb string
 	 * @param $args array
	 * @param $message string Location for the plugin to put a result msg
 	 * @return boolean
 	 */
	function manage($args, $request)
	{
		return parent::manage($args, $request);


		// if (!parent::manage($verb, $args)) return false;
		// switch ($verb) {
		// 	case 'statistics':
		// 		Request::redirect(null, 'statistics');
		// 		return false;
		// 	default:
		// 		// Unknown management verb
		// 		assert(false);
		// 		return false;
		// }
	}
}
