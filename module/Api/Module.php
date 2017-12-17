<?php

namespace Api;

use Api\Model\GamesTable;
use Api\Model\ParticipantsTable;
use Api\Model\PlayersTable;
use Zend\Mvc\MvcEvent;

/**
 *
 */
class Module
{
	/**
	 * @param MvcEvent $e
	 */
	public function onBootstrap($e)
	{
		/** @var \Zend\ModuleManager\ModuleManager $moduleManager */
		$moduleManager = $e->getApplication()->getServiceManager()->get('modulemanager');
		/** @var \Zend\EventManager\SharedEventManager $sharedEvents */
		$sharedEvents = $moduleManager->getEventManager()->getSharedManager();

		$sharedEvents->attach('Zend\Mvc\Controller\AbstractRestfulController', MvcEvent::EVENT_DISPATCH, array($this, 'postProcess'), -100);
		$sharedEvents->attach('Zend\Mvc\Application', MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'errorProcess'), 999);
	}

	/**
	 * return array
	 */
	public function getAutoloaderConfig()
	{
		return array(
			'Zend\Loader\ClassMapAutoloader' => array(
				__DIR__ . '/autoload_classmap.php',
			),
			'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
					__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
				),
			),
		);
	}

	/**
	 * @return array
	 */
	public function getConfig()
	{
		return include __DIR__ . '/config/module.config.php';
	}

	/**
	 * @param MvcEvent $e
	 * @return null|\Zend\Http\PhpEnvironment\Response
	 */
	public function postProcess(MvcEvent $e)
	{
		$routeMatch = $e->getRouteMatch();
		$formatter = $routeMatch->getParam('formatter', false);

		/** @var \Zend\Di\Di $di */
		$di = $e->getTarget()->getServiceLocator()->get('di');

		if ($formatter !== false) {
			if ($e->getResult() instanceof \Zend\View\Model\ViewModel) {
				if (is_array($e->getResult()->getVariables())) {
					$vars = $e->getResult()->getVariables();
				} else {
					$vars = null;
				}
			} else {
				$vars = $e->getResult();
			}

			/** @var PostProcessor\AbstractPostProcessor $postProcessor */
			$postProcessor = $di->get($formatter . '-pp', array(
				'response' => $e->getResponse(),
				'vars' => $vars,
			));

			$postProcessor->process();

			return $postProcessor->getResponse();
		}

		return null;
	}

	/**
	 * @param MvcEvent $e
	 * @return null|\Zend\Http\PhpEnvironment\Response
	 */
	public function errorProcess(MvcEvent $e)
	{
		/** @var \Zend\Di\Di $di */
		$di = $e->getApplication()->getServiceManager()->get('di');

		$eventParams = $e->getParams();

		/** @var array $configuration */
		$configuration = $e->getApplication()->getConfig();

		$vars = array();
		if (isset($eventParams['exception'])) {
			/** @var \Exception $exception */
			$exception = $eventParams['exception'];

			if ($configuration['errors']['show_exceptions']['message']) {
				$vars['error-message'] = $exception->getMessage();
			}
			if ($configuration['errors']['show_exceptions']['trace']) {
				$vars['error-trace'] = $exception->getTrace();
			}
		}

		if (empty($vars)) {
			$vars['error'] = 'Something went wrong';
		}

		/** @var PostProcessor\AbstractPostProcessor $postProcessor */
		$postProcessor = $di->get(
			$configuration['errors']['post_processor'],
			array('vars' => $vars, 'response' => $e->getResponse())
		);

		$postProcessor->process();

		if (
			$eventParams['error'] === \Zend\Mvc\Application::ERROR_CONTROLLER_NOT_FOUND ||
			$eventParams['error'] === \Zend\Mvc\Application::ERROR_ROUTER_NO_MATCH
		) {
			$e->getResponse()->setStatusCode(\Zend\Http\PhpEnvironment\Response::STATUS_CODE_501);
		} else {
			$e->getResponse()->setStatusCode(\Zend\Http\PhpEnvironment\Response::STATUS_CODE_500);
		}

		$e->stopPropagation();

		return $postProcessor->getResponse();
	}

    /**
     * @return array
     */
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Api\Model\GamesTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new GamesTable($dbAdapter);
                    return $table;
                },
                'Api\Model\PlayersTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new PlayersTable($dbAdapter);
                    return $table;
                },
                'Api\Model\ParticipantsTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new ParticipantsTable($dbAdapter);
                    return $table;
                },
            ),
        );
    }
}
