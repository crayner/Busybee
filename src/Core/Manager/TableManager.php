<?php
namespace App\Core\Manager;

use Doctrine\ORM\EntityManagerInterface;

class TableManager
{
	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;

	/**
	 * TableManager constructor.
	 *
	 * @param EntityManagerInterface $entityManager
	 */
	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	/**
	 * @param string $entityName
	 *
	 * @return bool
	 */
	public function isTableInstalled(string $entityName)
	{
		if (class_exists($entityName))
		{
			$metaData = $this->entityManager->getClassMetadata('\\' . $entityName);
			$schema   = $this->entityManager->getConnection()->getSchemaManager();

			return $schema->tablesExist([$metaData->table['name']]);
		}

		return false;
	}

}