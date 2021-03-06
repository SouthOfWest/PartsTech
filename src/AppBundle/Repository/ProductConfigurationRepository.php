<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductConfiguration;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;

/**
 * ProductConfigurationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductConfigurationRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param ProductConfiguration $configuration
     * @return ProductConfiguration
     */
    public function save(ProductConfiguration $configuration)
    {
        $this->_em->persist($configuration);
        $this->_em->flush();

        return $configuration;
    }

    /**
     * @param ProductConfiguration $configuration
     * @return int
     */
    public function findIdByValue(ProductConfiguration $configuration)
    {
        $result = $this->createQueryBuilder("c")
            ->select('c.configId')
            ->where("c.url = :url")
            ->andWhere("c.value = :value")
            ->setMaxResults(1)
            ->setParameters([
                'value' => $configuration->getValue(),
                'url' => $configuration->getUrl()
            ])
            ->getQuery()
            ->getOneOrNullResult(Query::HYDRATE_SINGLE_SCALAR);

        return (int) $result;
    }

    /**
     * @return int
     */
    public function lastId()
    {
        return (int) $this->createQueryBuilder("c")
            ->select("MAX(c.id) as maxId")
            ->getQuery()
            ->getOneOrNullResult(Query::HYDRATE_SINGLE_SCALAR);
    }

    /**
     * @param string $url
     * @return array
     */
    public function findByCategoryUrl($url)
    {
        return $this->createQueryBuilder("conf")
            ->select('conf')
            ->leftJoin(Product::class, "p", Join::WITH, "p.id = conf.product")
            ->leftJoin(Category::class, "cat", Join::WITH, "p.categoryId = cat.id")
            ->where("cat.url = :url")
            ->groupBy("conf.configId")
            ->setParameter("url", $url)
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);
    }

    /**
     * @param ProductConfiguration $configuration
     * @return int
     */
    public function getExistingId(ProductConfiguration $configuration)
    {
        $id = $this->findIdByValue($configuration);

        return empty($id)?$this->lastId():$id;
    }
}
