<?php
namespace MailPlus\MailPlus\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;
use Magento\Quote\Model\Quote;
use MailPlus\MailPlus\Model\QuoteConversion;
use MailPlus\MailPlus\Model\QuoteConversionFactory;
use MailPlus\MailPlus\Model\ResourceModel\QuoteConversion as QuoteConversionResource;

class CreateCartObserver implements ObserverInterface
{
    private $quoteConversionFactory;
    private $cookieManager;
    private $quoteConversionResource;

    /**
     * CreateCartObserver constructor.
     * @param PhpCookieManager $cookieManager
     * @param QuoteConversionFactory $quoteConversionFactory
     * @param QuoteConversionResource $quoteConversionResource
     */
    public function __construct(
        PhpCookieManager $cookieManager,
        QuoteConversionFactory $quoteConversionFactory,
        QuoteConversionResource $quoteConversionResource
    ) {
        $this->quoteConversionFactory = $quoteConversionFactory;
        $this->cookieManager = $cookieManager;
        $this->quoteConversionResource = $quoteConversionResource;
    }

    public function execute(Observer $observer)
    {
        $mpId = $this->cookieManager->getCookie("mpId");
        if (!isset($mpId)) {
            return;
        }
        //Only use mpId for one cart creation
        $this->cookieManager->deleteCookie("mpId");
        /** @var Quote $quote */
        $quote = $observer->getData('quote');

        /** @var QuoteConversion $quoteConversion */
        $quoteConversion = $this->quoteConversionFactory->create();
        $this->quoteConversionResource->load($quoteConversion, $quote->getId());
        if (empty($quoteConversion->getMailPlusId())) {
            $quoteConversion->setMailPlusId($mpId);
            $quoteConversion->setQuoteId($quote->getId());
            $quoteConversion->setIsObjectNew(true);
        }
        $this->quoteConversionResource->save($quoteConversion);
    }
}
