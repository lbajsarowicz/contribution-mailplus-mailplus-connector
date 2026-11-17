<?php

namespace MailPlus\MailPlus\Plugin;

use DateTime;
use Magento\CatalogRule\Api\CatalogRuleRepositoryInterface;
use MailPlus\MailPlus\Model\CatalogRuleUpdatedAt;
use MailPlus\MailPlus\Model\CatalogRuleUpdatedAtFactory;
use MailPlus\MailPlus\Model\ResourceModel\CatalogRuleUpdatedAt as CatalogRuleUpdatedAtResource;

class CatalogRuleRepositoryPlugin
{
    private $_catalogRuleUpdatedAtFactory;
    private $catalogRuleUpdatedAtResource;


    /**
     * CatalogRuleRepositoryPlugin constructor.
     */
    public function __construct(
        CatalogRuleUpdatedAtFactory $catalogRuleUpdatedAtFactory,
        CatalogRuleUpdatedAtResource $catalogRuleUpdatedAtResource
    ) {
        $this->_catalogRuleUpdatedAtFactory = $catalogRuleUpdatedAtFactory;
        $this->catalogRuleUpdatedAtResource = $catalogRuleUpdatedAtResource;
    }


    /**
     * @param CatalogRuleRepositoryInterface $subject
     * @param \Magento\CatalogRule\Model\Rule $result
     * @return \Magento\CatalogRule\Api\Data\RuleInterface
     */
    public function afterSave(CatalogRuleRepositoryInterface $subject, $result)
    {
        $dateTime = (new DateTime())->format('c');

        /** @var CatalogRuleUpdatedAt $ruleUpdatedAt */
        $ruleUpdatedAt = $this->_catalogRuleUpdatedAtFactory->create();
        $this->catalogRuleUpdatedAtResource->load($ruleUpdatedAt, $result->getRuleId());
        if (empty($ruleUpdatedAt->getUpdatedAt())) {
            $ruleUpdatedAt->setUpdatedAt($dateTime);
            $ruleUpdatedAt->setCatalogRuleId($result->getRuleId());
            $ruleUpdatedAt->setIsObjectNew(true);
        } else {
            $ruleUpdatedAt->setUpdatedAt($dateTime);
        }
        $this->catalogRuleUpdatedAtResource->save($ruleUpdatedAt);

        return $result;
    }
}
