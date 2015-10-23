<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
/**
 * ...and connect the rest of 'Pages' controller's URLs.
 */
	Router::connect('/share/:_sid', array('controller' => 'histories', 'action' => 'view'), array('pass' => array('_sid')));
	Router::connect('/share/a/:lidoRecID', array('controller' => 'histories', 'action' => 'view_item'), array('pass' => array('lidoRecID')));
	Router::connect('/artefacts/fetch_more', array('controller' => 'artefacts', 'action' => 'fetch_more'));

	Router::connect('/export', array('controller' => 'exports', 'action' => 'export_data'));

	// tracking
	Router::connect('/t/visit', array('controller' => 'trackers', 'action' => 'record_visit'));
	Router::connect('/t/action', array('controller' => 'trackers', 'action' => 'record_action'));

	Router::connect('/map/get_artefacts_by_keyword', array('controller' => 'artefacts', 'action' => 'explore_keyword'));
	Router::connect('/map/get_keywords_by_lido_id', array('controller' => 'artefacts', 'action' => 'explore_artefact'));

	Router::connect('/session/keep_alive', array('controller' => 'artefacts', 'action' => 'keep_alive'));
	Router::connect('/session/session_data_map', array('controller' => 'artefacts', 'action' => 'session_data_map'));
	Router::connect('/session/record_click', array('controller' => 'artefacts', 'action' => 'record_click'));
	Router::connect('/session/remove_artefact_from_session', array('controller' => 'artefacts', 'action' => 'remove_artefact_from_session'));

  Router::connect('/import', array('controller' => 'imports', 'action' => 'import'));
  Router::connect('/import_images', array('controller' => 'imports', 'action' => 'import_images'));
	// Router::connect('/:_sid', array('controller' => 'histories', 'action' => 'view'), array('pass' => array('_sid')));
	Router::connect('/', array('controller' => 'artefacts', 'action' => 'scroll2'));
/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
	CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
	require CAKE . 'Config' . DS . 'routes.php';
