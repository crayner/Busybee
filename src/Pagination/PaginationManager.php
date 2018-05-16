<?php
namespace App\Pagination;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

/**
 * Pagination Manager
 *
 * @version    25th October 2016
 * @since      25th October 2016
 * @author     Craig Rayner
 */
abstract class PaginationManager implements PaginationInterface
{
	/**
	 * @var EntityRepository
	 */
	protected $repository;

	/**
	 * @var EntityManagerInterface
	 */
	protected $entityManager;

	/**
	 * @var Container
	 */
	protected $container;
	/**
	 * @var string
	 */
	protected $result;

	/**
	 * @var Query
	 */
	private $query;

	/**
	 * @var array
	 */
	private $sortBy = [];

	/**
	 * @var
	 */
	private $form;

	/**
	 * @var integer
	 */
	private $lastLimit;

	/**
	 * @var string
	 */
	private $search;

	/**
	 * @var integer
	 */
	private $total;

	/**
	 * @var integer
	 */
	private $offSet;

	/**
	 * @var integer
	 */
	private $pages;

	/**
	 * @var string
	 */
	private $choice;

	/**
	 * @var string
	 */
	private $lastSearch;

	/**
	 * @var array
	 */
	private $control = array();

	/**
	 * @var string
	 */
	private $sortByName;

	/**
	 * @var Session
	 */
	private $session;

	/**
	 * @var Router
	 */
	private $router;

	/**
	 * @var Router
	 */
	private $reDirect;

	/**
	 * @var string
	 */
	private $name = 'default';

	/**
	 * @var bool
	 */
	private $displaySearch = true;

	/**
	 * @var bool
	 */
	private $displaySort = true;

	/**
	 * @var bool
	 */
	private $displayResult = true;

	/**
	 * @var bool
	 */
	private $displayChoice = true;

	/**
	 * @var array
	 */
	private $injectedSearch;

    /**
     * @var RequestStack
     */
	private $stack;

	/**
	 * Constructor
	 *
	 * @version    25th October 2016
	 * @since      25th October 2016
	 *
	 * @param EntityManagerInterface $entityManager
	 * @param SessionInterface       $session
	 * @param RouterInterface        $router
	 * @param RequestStack           $requestStack
	 * @param FormFactoryInterface   $formFactory
	 */
	public function __construct(EntityManagerInterface $entityManager, RouterInterface $router, RequestStack $requestStack, FormFactoryInterface $formFactory)
	{
		$this->entityManager    = $entityManager;
		$this->repository = $entityManager->getRepository($this->repositoryName);
		$this->router     = $router;
        $this->stack      = $requestStack;
        $this->session    = null;

		$params          = [];
		$params['route'] = parse_url($this->getRoute($requestStack), PHP_URL_PATH);
		$route_params = $this->getRouteParams($requestStack);

		$this->path      = $router->generate($params['route'], is_array($route_params) ? $route_params : []);
		$this->setChoice(null);
		$this->setReDirect(false);

		$this->form           = $formFactory->createNamedBuilder(strtolower($this->getName()) . '_paginator', PaginationType::class, $this, $params)->getForm();
		$this->injectedSearch = [];
		$this->initialisePagination();
	}

	/**
	 * Initialise Pagination
	 */
	private function initialisePagination()
	{
		$x = $this->getSortByList();
		$this->sortBy = reset($x);
		$this->setSortByName(key($x));
	}
	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param $name
	 *
	 * @return $this
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * get Data Set
	 *
	 * @version    25th October 2016
	 * @since      25th October 2016
	 * @return    array    of Data
	 */
	public function getDataSet()
	{
		$this->pages  = intval(ceil($this->getTotal() / $this->getLimit()));
		$query = $this->buildQuery()
			->setFirstResult($this->getOffSet())
			->setMaxResults($this->getLimit())
			->getQuery();

		$this->result = $query
			->getResult();
		$this->writeSession();

		return $this->result;
	}

    /**
     * get Total
     *
     * @version    25th October 2016
     * @since      25th October 2016
     * @param bool $raw
     * @return    null|integer
     */
	public function getTotal($raw = false): ?int
	{
	    if ($raw)
	        return $this->total;

		if (empty($this->total))
		{
			$query = $this->buildQuery(true)
				->getQuery();
			$this->setTotal(intval($query
				->getSingleScalarResult()));
		}

		return $this->total;
	}

	/**
	 * set Total
	 *
	 * @version    25th October 2016
	 * @since      25th October 2016
	 *
	 * @param    integer $total
	 *
	 * @return    PaginationManager
	 */
	public function setTotal(int $total): PaginationManager
	{
		$this->total = $total > 0 ? $total : 0;

		return $this;
	}

	/**
	 * get Limit
	 *
	 * @version    25th October 2016
	 * @since      25th October 2016
	 * @return    PaginationManager
	 */
	public function getLimit()
	{
		$this->limit = intval($this->limit) < 10 ? 10 : intval($this->limit);

		return $this->limit;
	}

	/**
	 * set Limit
	 *
	 * @version    25th October 2016
	 * @since      25th October 2016
	 *
	 * @param    integer $limit
	 *
	 * @return    PaginationManager
	 */
	public function setLimit($limit)
	{
		$limit       = intval($limit);
		$this->limit = $limit < 10 ? 10 : $limit;

		return $this;
	}

	/**
	 * get OffSet
	 *
	 * @version    25th October 2016
	 * @since      25th October 2016
	 * @return    integer
	 */
	public function getOffSet()
	{
		return $this->offSet = !empty($this->offSet) ? $this->offSet : 0;
	}

	/**
	 * set OffSet
	 *
	 * @version    25th October 2016
	 * @since      25th October 2016
	 * ^ @param    integer $offSet
	 * @return    PaginationManager
	 */
	public function setOffSet($offSet)
	{
		$this->offSet = empty($offSet) ? 0 : intval($offSet);

		return $this;
	}

	/**
	 * @return $this
	 */
	protected function writeSession()
	{
		$pag = empty($this->getSession()->get('pagination')) ? [] : $this->getSession()->get('pagination');

		$cc = empty($pag[$this->paginationName]) ? [] : $pag[$this->paginationName];

		$cc['limit']  = $this->limit;
		$cc['search'] = $this->search;
		$cc['offSet'] = $this->offSet;
		$cc['choice'] = false !== $this->reDirect ? $this->reDirect : $this->choice;
		$cc['sortByName'] = $this->sortByName;

		$pag[$this->paginationName] = $cc;

		$this->getSession()->set('pagination', $pag);

		return $this;
	}

	/**
	 * get Sort By Name
	 *
	 * @version    25th October 2016
	 * @since      25th October 2016
	 * @return    string
	 */
	public function getSortByName()
	{
	    if (empty($this->sortByName))
            if (is_array($this->sortByList))
            {
                reset($this->sortByList);
                $this->sortByName = key($this->sortByList);
            }

		return $this->sortByName;
	}

	/**
	 * set Order By
	 *
	 * @version    25th October 2016
	 * @since      25th October 2016
	 * @return    PaginationManager
	 */
	public function setOrderBy()
	{
		if (!empty($this->getSortBy()))
		{
			foreach ($this->getSortBy() as $name => $order)
			{
				$this->query->addOrderBy($name, $order);
			}
		}

		return $this;
	}

	/**
	 * get Sort By
	 *
	 * @version    25th October 2016
	 * @since      25th October 2016
	 * @return    string
	 */
	public function getSortBy(): array
	{
		if (!empty($this->sortByList[$this->getSortByName()]))
			return $this->sortByList[$this->sortByName];

		return [];
	}

	/**
	 * set Search Where
	 *
	 * @version    25th October 2016
	 * @since      25th October 2016
	 * @return    PaginationManager
	 */
	public function setSearchWhere(): PaginationManager
	{
		$x = 0;
		if (!is_null($this->getSearch()))
		{
			foreach ($this->getSearchList() as $field)
			{
				$this->query->orWhere($field . ' LIKE :search' . $x);
				$this->query->setParameter('search' . $x++, '%' . $this->getSearch() . '%');
			}
		}

		if (! empty($this->getInjectedSearch())) {
            foreach ($this->getInjectedSearch() as $search) {
                $this->query->orWhere($search['where']);
                foreach($search['parameter'] as $name => $value) {
                    if (isset($search['type'][$name]))
                        $this->query->setParameter($name, $value, $search['type'][$name]);
                    else
                        $this->query->setParameter($name, $value);
                }
            }
        }
		return $this;
	}

	/**
	 * get Search
	 *
	 * @version    25th October 2016
	 * @since      25th October 2016
	 * @return     string
	 */
	public function getSearch()
	{
		return $x = empty($this->search) ? null : '%' . $this->search . '%';
	}

	/**
	 * set Search
	 *
	 * @version    25th October 2016
	 * @since      25th October 2016
	 *
	 * @param    string $search
	 *
	 * @return    PaginationManager
	 */
	public function setSearch($search)
	{
		$this->search = filter_var($search);

		return $this;
	}

	/**
	 * get Search List
	 *
	 * @version    25th October 2016
	 * @since      25th October 2016
	 * @return      array
	 */
	public function getSearchList(): array
	{
		return is_array($this->searchList) ? $this->searchList : [];
	}

	/**
	 * set Search List
	 *
	 * @version    25th October 2016
	 * @since      25th October 2016
	 *
	 * @param    array $searchList
	 *
	 * @return    PaginationManager
	 */
	public function setSearchList(array $searchList)
	{
		$this->searchList = $searchList ;

		return $this;
	}

	/**
	 * get Search Property
	 *
	 * @version    23rd May 2017
	 * @since      23rd May 2017
	 * @return    string
	 */
	public function getSearchProperty()
	{
		return empty($this->search) ? 'null' : $this->search;
	}

	/**
	 * get Pages
	 *
	 * @version    25th October 2016
	 * @since      25th October 2016
	 * @return    query
	 */
	public function getPages()
	{
		$this->pages = intval($this->pages) < 1 ? 1 : intval($this->pages);

		return $this->pages;
	}

	/**
	 * get Sort List
	 *
	 * @version    25th October 2016
	 * @since      25th October 2016
	 * @return    array
	 */
	public function getSortList()
	{
		$sortByList = [];
		if (!empty($this->sortByList) && is_array($this->sortByList))
			foreach ($this->sortByList as $name => $w)
				$sortByList[$name] = $name;

		return $sortByList;
	}

	/**
	 * inject Request
	 *
	 * @version    25th October 2016
	 * @since      25th October 2016
	 *
	 * @param Request $request
	 *
	 * @return PaginationManager
	 */
	public function injectRequest(Request $request)
	{
		$this->getForm()->handleRequest($request);

		if (!$this->form->isSubmitted())
		{
			$this->post = false;
			$this->resetPagination();
			$last = $this->getSession()->get('pagination');

			if (!empty($last[$this->paginationName]))
			{
				$last = $last[$this->paginationName];
				$this->setSearch($last['search']);
				$this->form['sortByName']->setData($last['search']);
				$this->setLimit($last['limit']);
				$this->form['limit']->setData($last['limit']);
				$this->setLastLimit($last['limit']);
				$this->form['lastLimit']->setData($last['limit']);
				$this->limit = $last['limit'];
				$this->setOffSet($last['offSet']);
				$this->setChoice($last['choice']);
				if ($last['choice'] !== $this->path)
					$this->setReDirect($last['choice']);
				$this->setSortByName($last['sortByName']);
			}

			$this->getTotal();
		}
		else
		{
			$this->post = true;
			$this->setSearch($this->form['searchData']->getData());
			$this->setLastSearch($this->form['lastSearch']->getData());
			$this->setOffSet($this->form['offSet']->getData());
			if ($this->form->has('lastChoice') && !empty($this->form['lastChoice']->getData()) && $this->form['lastChoice']->getData() !== $this->form['choice']->getData())
				$this->resetPagination();
			if (trim($this->getSearch(), '%') !== trim($this->getLastSearch(), '%'))
				$this->resetPagination();
			if ($this->form->has('choice'))
				$this->setChoice($this->form['choice']->getData());
			else
				$this->setChoice(null);
			$this->setSearch($this->form['searchData']->getData());
			$this->setLastSearch($this->form['lastSearch']->getData());

			if ($this->getLimit() > $this->getLastLimit())
				if ($this->getOffSet() + $this->getLimit() > $this->getTotal())
					$this->setOffSet($this->getTotal() - $this->getLimit() < 0 ? 0 : $this->getTotal() - $this->getLimit());

			$this->setLimit($this->form['limit']->getData());
			$this->setLastLimit($this->form['limit']->getData());

			$this->setSortByName($this->form['sortByName']->getData());
			$this->getTotal();
			$this->managePost($request);
		}
		$this->form = $this->getForm()->createView();

		return $this;
	}

	/**
	 * get Form
	 *
	 * @version    25th October 2016
	 * @since      25th October 2016
	 * @return    Object
	 */
	public function getForm()
	{
		return $this->form;
	}

	/**
	 * Reset Pagination
	 *
	 * @version    25th October 2016
	 * @since      25th October 2016
	 * @return    PaginationManager
	 */
	private function resetPagination()
	{
		$this->initialisePagination();
		$this->total  = 0;
		$this->offSet = 0;

		return $this;
	}

	/**
	 * get Last Search
	 *
	 * @version    25th October 2016
	 * @since      25th October 2016
	 * @return    string
	 */
	public function getLastSearch()
	{
		return $this->lastSearch = empty($this->lastSearch) ? '' : $this->lastSearch;
	}

	/**
	 * set Last Search
	 *
	 * @version    25th October 2016
	 * @since      25th October 2016
	 *
	 * @param $lastSearch
	 *
	 * @return PaginationManager
	 */
	public function setLastSearch($lastSearch)
	{
		$this->lastSearch = $lastSearch;
		if (empty($lastSearch))
			$this->lastSearch = null;

		return $this;
	}

	/**
	 * get Last Limit
	 *
	 * @version    25th October 2016
	 * @since      25th October 2016
	 * @return    integer
	 */
	public function getLastLimit()
	{
		$this->lastLimit = intval($this->lastLimit) < 10 ? 10 : intval($this->lastLimit);

		return $this->lastLimit;
	}

	/**
	 * set Last Limit
	 *
	 * @version    25th October 2016
	 * @since      25th October 2016
	 *
	 * @param    integer $limit
	 *
	 * @return    PaginationManager
	 */
	public function setLastLimit($limit)
	{
		$limit           = intval($limit);
		$this->lastLimit = $limit < 10 ? 10 : $limit;

		return $this;
	}

	/**
	 * Manage Post
	 *
	 * @version 15th February 2017
	 * @since   25th October 2016
	 * @return  PaginationManager
	 */
	public function managePost(Request $request)
	{
		$data = $request->get('paginator');
		if (!empty($data))
		{
			if (array_key_exists('prev', $data))
				$this->getPrev();
			if (array_key_exists('next', $data))
				$this->getNext();
		}
		// if ajax is used then ....
		switch ($this->control)
		{
			case 'paginatorNext':
				$this->getNext();
				break;
			case 'paginatorPrev':
				$this->getPrev();
				break;
		}

		return $this;
	}

	/**
	 * get Prev
	 *
	 * @version    25th October 2016
	 * @since      25th October 2016
	 * @return    integer
	 */
	private function getPrev()
	{
		$offSet = $this->getOffSet() - $this->getLimit();

		return $this->checkOffset($offSet);
	}

	/**
	 * check OffSet
	 *
	 * @version    25th October 2016
	 * @since      25th October 2016
	 *
	 * @param   integer $offSet
	 *
	 * @return int
	 */
	private function checkOffSet($offSet)
	{
		if ($offSet >= $this->total)
			$offSet = $this->total - $this->getLimit();
		if ($offSet < 0)
			$offSet = 0;
		$this->setOffset($offSet);

		return $offSet;
	}

	/**
	 * get Next
	 *
	 * @version    25th October 2016
	 * @since      25th October 2016
	 * @return    integer
	 */
	private function getNext()
	{
		$offSet = $this->getOffSet() + $this->getLimit();
		if ($offSet > $this->getTotal())
			$offSet = $this->getOffSet();

		return $this->checkOffSet($offSet);
	}

	/**
	 * get Current Page
	 *
	 * @version    25th October 2016
	 * @since      25th October 2016
	 * @return    integer
	 */
	public function getCurrentPage()
	{
		return intval($this->getOffSet() / $this->getLimit()) + 1;
	}

	/**
	 * get Total Pages
	 *
	 * @version    25th October 2016
	 * @since      25th October 2016
	 * @return    integer
	 */
	public function getTotalPages()
	{
		return $this->pages;
	}

	/**
	 * get Result
	 *
	 * @version    25th October 2016
	 * @since      25th October 2016
	 * @return    array Result
	 */
	public function getResult()
	{
		return $this->result ?: [];
	}

	/**
	 * @return int
	 */
	public function getFirstRecord()
	{
		return $this->offSet + 1;
	}

	/**
	 * @return int
	 */
	public function getLastRecord()
	{
		return $this->offSet + $this->getLimit() > $this->getTotal() ? $this->getTotal() : $this->offSet + $this->getLimit();
	}

	/**
	 * @return array
	 */
	public function getChoices()
	{
		return isset($this->choices) ? $this->choices : [];
	}


	public function getChoice()
	{
		if (is_array($this->choice))
			throw new \InvalidArgumentException('NO NO NO Choice is not an array ever.');

		return $this->choice;
	}

	public function setChoice($choice)
	{
		if (is_array($choice))
			throw new \InvalidArgumentException('NO NO NO Choice is not an array ever, so don\'t set it that way');

		$this->choice = $choice;

		return $this;
	}

    /**
     * @return bool|Router
     */
    public function getReDirect()
	{
	    if (empty($this->reDirect))
	        $this->reDirect = false;
		return $this->reDirect;
	}

    /**
     * @param bool|Router $x
     * @return $this
     */
    public function setReDirect($x)
	{
	    if (empty($x))
	        $x = false;
		$this->reDirect = $x;

		return $this;
	}

	public function getLastChoice()
	{
		return $this->choice;
	}

	/**
	 * @return QueryBuilder
	 */
	public function getQuery(): QueryBuilder
	{
		return $this->query;
	}

	/**
	 * @return bool
	 */
	public function isDisplaySearch(): bool
	{
		return $this->displaySearch;
	}

	/**
	 * @param bool $displaySearch
	 */
	public function setDisplaySearch(bool $displaySearch)
	{
		$this->displaySearch = $displaySearch;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isDisplaySort(): bool
	{
	    if (!is_array($this->sortByList) || count($this->sortByList) <= 1)
	        $this->setDisplaySort(false);

		return $this->displaySort;
	}

	/**
	 * @param bool $displaySort
	 */
	public function setDisplaySort(bool $displaySort)
	{
		$this->displaySort = $displaySort;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isDisplayResult(): bool
	{
		return $this->displayResult;
	}

	/**
	 * @param bool $displayResult
	 */
	public function setDisplayResult(bool $displayResult)
	{
		$this->displayResult = $displayResult;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isDisplayChoice(): bool
	{
		return $this->displayChoice;
	}

	/**
	 * @param bool $displayChoice
	 */
	public function setDisplayChoice(bool $displayChoice)
	{
		$this->displayChoice = $displayChoice;

		return $this;
	}

    /**
     * addInjectedSearch
     *
     * @param array|null $search
     * @return PaginationManager
     */
    public function addInjectedSearch(?array $search): PaginationManager
	{
        if (isset($search['where']) && isset($search['parameter']))
    	    $this->injectedSearch[] = $search;
        return $this;
	}

	public function getIdName()
	{
		return strtolower($this->getName() . '_pagination');
	}

	/**
	 * @param string|null $sortByName
	 *
	 * @return PaginationManager
	 */
	public function setSortByName(string $sortByName = null): PaginationManager
	{
        if (empty($sortByName)) {
            reset($this->sortByList);
            $sortByName = key($this->sortByList);
        }
		$this->sortByName = $sortByName;

		return $this;
	}

    /**
     * @param int $pages
     * @return PaginationManager
     */
    public function setPages(int $pages): PaginationManager
    {
        $this->pages = $pages;
        return $this;
    }

    /**
     * @return null|array
     */
    public function getInjectedSearch(): array
    {
        return $this->injectedSearch ?: [];
    }

    /**
     * @param array $injectedSearch
     * @return PaginationManager
     */
    public function setInjectedSearch(array $injectedSearch): PaginationManager
    {
        $this->injectedSearch = $injectedSearch;
        return $this;
    }

    /**
     * @return RequestStack
     */
    public function getStack(): RequestStack
    {
        return $this->stack;
    }

    /**
     * @return Session
     */
    public function getSession(): Session
    {
        if (empty($this->session))
            $this->session = $this->getStack()->getCurrentRequest()->getSession();
        return $this->session;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @param array $sortBy
     * @return PaginationManager
     */
    private function setSortBy(array $sortBy): PaginationManager
    {
        $this->sortBy = $sortBy;
        return $this;
    }

    /**
	 * initiate Query
	 *
	 * @version    25th October 2016
	 * @since      25th October 2016
	 *
	 * @param    boolean $count
	 *
	 * @return    Query
	 */
	protected function initiateQuery($count = false)
	{
		$this->query = $this->repository->createQueryBuilder($this->getAlias());
		if ($count)
			$this->query->select('COUNT(' . $this->getAlias() . ')');

		return $this->query;
	}

	/**
	 * set Select
	 *
	 * @version    27th October 2016
	 * @since      27th October 2016
	 * @return    string
	 */
	public function getAlias(): ?string
	{
		return $this->alias;
	}

	/**
	 * set Join
	 *
	 * @version     13th February 2018
	 * @since       27th October 2016
	 * @return      PaginationManager
	 */
	protected function setQueryJoin()
	{
        if (empty($this->join) || !is_array($this->join)) return $this;
		foreach ($this->join as $name => $pars)
		{
            $type = empty($pars['type']) ? 'join' : $pars['type'];
			$this->query->$type($name, $pars['alias']);
		}

		return $this;
	}

	/**
	 * set Query Select
	 *
	 * @version     13th February 2018
	 * @since       27th October 2016
	 * @return      PaginationManager
	 */
	protected function setQuerySelect()
	{
	    $this->query->select($this->getAlias() . ' AS entity');

		if (empty($this->select)  || !is_array($this->select)) return $this;
		foreach ($this->select as $name)
		{
			if (is_string($name))
				$this->query->addSelect($name);
			elseif (is_array($name))
			{
				$k      = key($name);
				if ($k == '0')
				    $k = 'entity';
				$concat = new Query\Expr\Func('CONCAT', $name[$k]);
				$concat .= ' as ' . $k;
				$concat = str_replace(',', ',\' \',', $concat);
				$this->query->addSelect($concat);
			}

		}

		return $this;
	}

    /**
     * set Join
     *
     * @version    27th October 2016
     * @since      27th October 2016
     * @param array $join
     * @return    PaginationManager
     */
	public function setJoin(array $join) // Scripted Call
	{
		$this->join = $join;
		return $this;
	}

	/**
	 * set Select
	 *
	 * @version    27th October 2016
	 * @since      27th October 2016
	 * @return    PaginationManager
	 */
	private function setSelect($select) // Scripted Call
	{
		$this->select = $select;

		return $this;
	}

	/**
	 * set Alias
	 *
	 * @version    27th October 2016
	 * @since      27th October 2016
	 * @return    PaginationManager
	 */
	private function setAlias($alias) // Scripted Call
	{
		$this->alias = $alias;

		return $this;
	}

	/**
	 * Add Join
	 *
	 * @param $name
	 * @param $type
	 * @param $alias
	 */
	public function addJoin($name, $type, $alias)
	{
		$this->join[$name] = ['type' => $type, 'alias' => $alias];
	}

	/**
	 * Add Search List
	 *
	 * @param $name
	 */
	public function addSearchList($name)
	{
		$this->searchList[] = $name;
	}

	/**
	 * Set TransDomain
	 *
	 * @param $domain
	 *
	 * @return $this
	 */
	private function setTransDomain($domain): PaginationManager
	{
		$this->transDomain = $domain;

		return $this;
	}

    /**
     * @return string
     */
    public function getTransDomain()
	{
		return isset($this->transDomain) ? $this->transDomain : 'Pagination' ;
	}

    /**
     * @param array $sortByList
     * @return ClassPagination
     */
    public function setSortByList(array $sortByList): PaginationManager
    {
        $this->sortByList = $sortByList;
        return $this;
    }


    /**
     * @return array
     */
    public function getSortByList(): array
    {
        return $this->sortByList;
    }

    /**
     * @param RequestStack $requestStack
     * @return string
     */
    private function getRoute(RequestStack $requestStack): string
    {
        $route = $requestStack->getCurrentRequest()->get('_route');
        if (! empty($route))
            return $route;
            $forward = $requestStack->getCurrentRequest()->get('_forwarded');
        if ($forward)
            $route = $forward->get('_route');

        if (! empty($route))
            return $route;
        return 'home';
    }

    /**
     * @param RequestStack $requestStack
     * @return array
     */
    private function getRouteParams(RequestStack $requestStack): array
    {
        $route = $requestStack->getCurrentRequest()->get('_route_params');
        if (! empty($route))
            return $route;
        $forward = $requestStack->getCurrentRequest()->get('_forwarded');
        if ($forward)
            $route = $forward->get('_route_params');

        if (! empty($route))
            return $route;
        return [];
    }

    /**
     * @param string $sortBy
     * @return array
     */
    public function getSortResult(string $sortBy): array
    {
        $result = new ArrayCollection($this->getResult());

        $iterator = $result->getIterator();
        $iterator->uasort(
            function ($a, $b) use ($sortBy) {
                return ($a['entity']->$sortBy() < $b['entity']->$sortBy()) ? -1 : 1;
            }
        );

        return iterator_to_array($iterator, false);

    }
}
