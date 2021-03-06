<?php
/**
 * Joomla! Framework Website
 *
 * @copyright  Copyright (C) 2014 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Joomla\FrameworkWebsite;

use DebugBar\DebugBar;
use Joomla\Application\AbstractWebApplication;
use Joomla\Controller\ControllerInterface;
use Joomla\DI\{
	ContainerAwareInterface, ContainerAwareTrait
};
use Joomla\Router\Router;

/**
 * Web application class
 */
class WebApplication extends AbstractWebApplication implements ContainerAwareInterface
{
	use ContainerAwareTrait;

	/**
	 * Application debug bar
	 *
	 * @var  DebugBar
	 */
	private $debugBar;

	/**
	 * Application router
	 *
	 * @var  Router
	 */
	private $router;

	/**
	 * Checks the accept encoding of the browser and compresses the data before sending it to the client if possible.
	 *
	 * @return  void
	 */
	protected function compress()
	{
		if (!$this->get('debug', false))
		{
			parent::compress();
		}
	}

	/**
	 * Method to run the application routines
	 *
	 * @return  void
	 */
	protected function doExecute()
	{
		if ($this->debugBar)
		{
			/** @var \DebugBar\DataCollector\TimeDataCollector $collector */
			$collector = $this->debugBar['time'];

			$collector->startMeasure('routing');
		}

		$route = $this->router->parseRoute($this->get('uri.route'), $this->input->getMethod());

		// Add variables to the input if not already set
		foreach ($route->getRouteVariables() as $key => $value)
		{
			$this->input->def($key, $value);
		}

		if ($this->debugBar)
		{
			/** @var \DebugBar\DataCollector\TimeDataCollector $collector */
			$collector = $this->debugBar['time'];

			$collector->stopMeasure('routing');
		}

		if ($this->debugBar)
		{
			/** @var \DebugBar\DataCollector\TimeDataCollector $collector */
			$collector = $this->debugBar['time'];

			$collector->startMeasure('controller');
		}

		/** @var ControllerInterface $controller */
		$controller = $this->getContainer()->get($route->getController());
		$controller->execute();

		if ($this->debugBar)
		{
			/** @var \DebugBar\DataCollector\TimeDataCollector $collector */
			$collector = $this->debugBar['time'];

			$collector->stopMeasure('controller');
		}
	}

	/**
	 * Set the application's debug bar
	 *
	 * @param   DebugBar  $debugBar  DebugBar object to set
	 *
	 * @return  $this
	 */
	public function setDebugBar(DebugBar $debugBar) : WebApplication
	{
		$this->debugBar = $debugBar;

		return $this;
	}

	/**
	 * Set the application's router
	 *
	 * @param   Router  $router  Router object to set
	 *
	 * @return  $this
	 */
	public function setRouter(Router $router) : WebApplication
	{
		$this->router = $router;

		return $this;
	}
}
