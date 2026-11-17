<?php
namespace MailPlus\MailPlus\Model;

class Subscriber extends \Magento\Newsletter\Model\Subscriber implements \MailPlus\MailPlus\Api\Data\SubscriberInterface
{
    public function setId($value)
    {
        return parent::setId($value);
    }

    public function getId()
    {
        return parent::getId();
    }

    public function setEmail($value)
    {
        return parent::setEmail($value);
    }

    public function getEmail()
    {
        return parent::getEmail();
    }

    public function setStoreId($value)
    {
        return parent::setStoreId($value);
    }

    public function getStoreId()
    {
        return parent::getStoreId();
    }

    public function setWebsiteId($value)
    {
        return parent::setWebsiteId($value);
    }

    public function getWebsiteId()
    {
        return parent::getWebsiteId();
    }

    public function setCustomerId($value)
    {
        return parent::setCustomerId($value);
    }

    public function getCustomerId()
    {
        return parent::getCustomerId();
    }

    public function setStatus($value)
    {
        return parent::setStatus($value);
    }

    public function getStatus()
    {
        return parent::getStatus();
    }
}
