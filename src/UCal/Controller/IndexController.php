<?php

namespace UCal\Controller;

use Zend\Mvc\Controller\AbstractActionController;

use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\Ldap as AuthAdapter;
use Zend\Authentication\Result;

use UCal\Model\ADE;

use Doctrine\ORM\EntityManager;

class IndexController extends AbstractActionController
{
	/**
	 * @var Doctrine\ORM\EntityManager
	 */
	protected $em;
	
    public function indexAction()
    {
    	$login     = $this->params()->fromPost('login');
    	$password  = $this->params()->fromPost('password');
    	$resources = $this->params()->fromPost('resources');

    	if($login != null && $password != null) {
    		
    		$auth = new AuthenticationService();
    		
    		$config = $this->getServiceLocator()->get('Config');
    		
    		$username = sprintf($config['ucal']['username_format'], $login);

    		$adapter = new AuthAdapter($config['ucal']['ldap_options'], $username, $password);
    		
    		$result = $auth->authenticate($adapter);
    		
    		switch ($result->getCode()) {
    		
    			case Result::FAILURE_IDENTITY_NOT_FOUND:
    			case Result::FAILURE_CREDENTIAL_INVALID:
    				return new ViewModel(array('messages' => array('Identifiants invalides. Recommencez.')));
    				break;
    		
    			case Result::SUCCESS:
		    		$account = $this->getEntityManager()->find('UCal\Model\Account', $login);
		    		if($account == NULL) {
		    			$account = new \UCal\Model\Account();
		    			$account->login = $login;
		    		}
		
		    		$account->resourcesIds = $resources;
			    	$this->getEntityManager()->merge($account);
		    			
			    	$this->getEntityManager()->flush();
			    		
			    	return new ViewModel(array (
			    		'messages'     => array(),
			    		'login'        => $login,
			    		'resourcesIds' => $resources,
			    	));
    				break;
    			default:
    				return new ViewModel(array('messages' => array('Erreur de connexion.')));
    				break;
    		}
    	}

    	return new ViewModel(array('messages' => array()));
    }

    public function flowAction()
    {
    	$login = $this->params('login');
    	
	    $account = $this->getEntityManager()->find('UCal\Model\Account', $login);
    	if($account == NULL) {
    		$this->getResponse()->setStatusCode(400);
    		$viewModel = new ViewModel(array(
    			'message'   => 'L\'identifiant n\'a pas de configuration associée.',
    		));
    		$viewModel->setTemplate('error/index');
    		return $viewModel;
    	}

    	$resourcesIds = explode(',', $account->resourcesIds);
    	if(!array_product(array_map('is_numeric', $resourcesIds))) {
    		$this->getResponse()->setStatusCode(400);
    		$viewModel = new ViewModel(array(
    			'message' => 'Tous les resources ne sont pas des nombres entiers.',
    		));
    		$viewModel->setTemplate('error/index');
    		return $viewModel;
    	}
    	 
    	$allResourcesIds = $this->getParentsIds($resourcesIds);
    	 
    	$viewModel = new ViewModel(array(
    		'events' => $this->getEventsHavingResource(ADE::getWeeksRelative(array(-1, 0, 1, 2)), $allResourcesIds),
    	));
    	 
    	$viewModel->setTerminal(true);
    	 
    	return $viewModel;
    }

    private function array_values_recursive($array)
    {
    	$arrayValues = array();
    	foreach ($array as $value) {
    		if (is_scalar($value) OR is_resource($value)) {
    			$arrayValues[] = $value;
    		}
    		elseif (is_array($value)) {
    			$arrayValues = array_merge($arrayValues, $this->array_values_recursive($value));
    		}
    	}
    	return $arrayValues;
    }

	private function getParentsIds(array $resourcesIds)
	{
		$dql = 'SELECT r
				FROM UCal\Model\Resource r
				WHERE r.id IN (:rids)';
		
		$allIds = array();
		
		$resources = $this->getEntityManager()
			->createQuery($dql)
			->setParameter('rids', $resourcesIds)
			->getResult();
		
		foreach($resources as $resource) {
			$allIds = array_merge($allIds, $this->fetchParentIds($resource));
		}
		return array_unique(array_filter(array_values($allIds), 'strlen'));
	}
	
	private function fetchParentIds($resource)
	{
		if(!$resource) return array();

		$return = array($resource->id);

		try {
			$return = array_merge($return, $this->fetchParentIds($resource->father));
		} catch (\Exception $e) {
		}
		return $return;
	}

	private function getEventsHavingResource(array $weeks, array $resourcesIds)
	{
		$dql = 'SELECT e.id
				FROM UCal\Model\Event e
				JOIN e.resources r
				WHERE e.week IN (:weeks) and r.id IN (:rids)';
		
		$eventsIds = $this->getEntityManager()
			->createQuery($dql)
			->setParameter('rids', $resourcesIds)
			->setParameter('weeks', $weeks)
			->getResult();

		$eventsIds = $this->array_values_recursive($eventsIds);
		
		$qb = $this->getEntityManager()
			->getRepository('UCal\Model\Event')
			->createQueryBuilder('e')
			->select('e,r')
			->leftJoin('e.resources', 'r')
			->where('e.id in (:ids)')
			->setParameter('ids', $eventsIds);

		return $qb->getQuery()->getResult();
	}

	public function getEntityManager()
	{
		if (null === $this->em) {
			$this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
		}
		return $this->em;
	}

}
