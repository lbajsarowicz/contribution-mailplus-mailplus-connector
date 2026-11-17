<?php
namespace MailPlus\MailPlus\Model;

use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsFactory;
use Magento\Newsletter\Model\ResourceModel\Subscriber as SubscriberResource;
use Magento\Newsletter\Model\ResourceModel\Subscriber\Collection;
use Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory;
use MailPlus\MailPlus\Api\SubscriberRepositoryInterface;

class SubscriberRepository implements SubscriberRepositoryInterface
{

    protected $_subscriberCollectionFactory;
    protected $_searchResultsFactory;
    private $_subscriberFactory;
    private $subscriberResource;


    public function __construct(
        CollectionFactory $subscriberCollectionFactory,
        SearchResultsFactory $searchResultsFactory,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        SubscriberResource $subscriberResource
    ) {
        $this->_subscriberCollectionFactory = $subscriberCollectionFactory;
        $this->_searchResultsFactory = $searchResultsFactory;
        $this->_subscriberFactory = $subscriberFactory;
        $this->subscriberResource = $subscriberResource;
    }

    /**
     * {@inheritdoc}
     */

    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var Collection $collection */
        $collection = $this->_subscriberCollectionFactory->create();

        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }

        $collection
            ->addSubscriberTypeField()
            ->showStoreInfo()
            ->setPageSize($searchCriteria->getPageSize())
            ->setCurPage($searchCriteria->getCurrentPage())
            ->load();

        /** @var \Magento\Framework\Api\SearchResults $searchResult */
        $searchResult = $this->_searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection $collection
     * @return void
     */
    protected function addFilterGroupToCollection(
        FilterGroup $filterGroup,
        Collection $collection
    ) {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $fields[] = $filter->getField();
            $conditionType = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $conditionValue = $conditionType != 'in' && $conditionType != 'nin'
                ? $filter->getValue() : explode(',', $filter->getValue());
            $conditions[] = [$conditionType => $conditionValue];
        }

        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
    }

    /**
     * Save a subscriber
     * @param \MailPlus\MailPlus\Api\Data\SubscriberInterface $subscriber
     * @return \MailPlus\MailPlus\Api\Data\SubscriberInterface
     */
    public function save(\MailPlus\MailPlus\Api\Data\SubscriberInterface $subscriber)
    {
        if ($subscriber->getId()) {
            /**
             * @var \Magento\Newsletter\Model\Subscriber
             */
            $loadedSubscriber = $this->_subscriberFactory->create();
            $this->subscriberResource->load($loadedSubscriber, $subscriber->getId());

            $loadedSubscriber->setStatus($subscriber->getStatus());
            $this->subscriberResource->save($loadedSubscriber);
            return $loadedSubscriber;
        }

        return $subscriber;
    }
}
