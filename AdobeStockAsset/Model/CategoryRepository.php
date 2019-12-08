<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAssetApi\Model\Category\Command\{SaveInterface, LoadByIdInterface, DeleteByIdInterface};
use Magento\AdobeStockAsset\Model\ResourceModel\Category\{
    Collection as CategoryCollection,
    CollectionFactory as CategoryCollectionFactory};
use Magento\AdobeStockAssetApi\Api\CategoryRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\{
    CategoryInterface,
    CategorySearchResultsInterface,
    CategorySearchResultsInterfaceFactory};
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Centralize common data access functionality for the Adobe Stock category.
 *
 *  Uses commands as proxy for those operations.
 */
class CategoryRepository implements CategoryRepositoryInterface
{

    /**
     * @var CategoryCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var JoinProcessorInterface
     */
    private $joinProcessor;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var CategorySearchResultsInterfaceFactory
     */
    private $searchResultFactory;

    /**
     * @var LoadByIdInterface
     */
    private $loadByIdCommand;

    /**
     * @var SaveInterface
     */
    private $saveCommand;

    /**
     * @var DeleteByIdInterface
     */
    private $deleteByIdCommand;

    /**
     * CategoryRepository constructor.
     *
     * @param CategoryCollectionFactory $collectionFactory
     * @param JoinProcessorInterface $joinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param CategorySearchResultsInterfaceFactory $searchResultFactory
     * @param LoadByIdInterface $loadByIdCommand
     * @param SaveInterface $saveCommand
     * @param DeleteByIdInterface $deleteByIdCommand
     */
    public function __construct(
        CategoryCollectionFactory $collectionFactory,
        JoinProcessorInterface $joinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        CategorySearchResultsInterfaceFactory $searchResultFactory,
        LoadByIdInterface $loadByIdCommand,
        SaveInterface $saveCommand,
        DeleteByIdInterface $deleteByIdCommand
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->joinProcessor = $joinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultFactory = $searchResultFactory;
        $this->loadByIdCommand = $loadByIdCommand;
        $this->saveCommand = $saveCommand;
        $this->deleteByIdCommand = $deleteByIdCommand;
    }

    /**
     * @inheritdoc
     */
    public function save(CategoryInterface $category): CategoryInterface
    {
        $this->saveCommand->execute($category);

        return $category;
    }

    /**
     * @inheritdoc
     */
    public function delete(CategoryInterface $category): void
    {
        $this->deleteByIdCommand->execute($category->getId());
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria) : CategorySearchResultsInterface
    {
        /** @var CategoryCollection $collection */
        $collection = $this->collectionFactory->create();
        $this->joinProcessor->process(
            $collection,
            CategoryInterface::class
        );

        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var CategorySearchResultsInterface $searchResults */
        $searchResults = $this->searchResultFactory->create();
        $searchResults->setItems($collection->getItems());
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function getById(int $categoryId) : CategoryInterface
    {
        $category = $this->loadByIdCommand->execute($categoryId);
        if (!$category->getId()) {
            throw new NoSuchEntityException(
                __(
                    'Adobe Stock asset category with id "%1" does not exist.',
                    $categoryId
                )
            );
        }
        return $category;
    }

    /**
     * @inheritdoc
     */
    public function deleteById(int $id): void
    {
        $this->deleteByIdCommand->execute($id);
    }
}
