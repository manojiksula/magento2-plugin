<?php

namespace Springbot\Main\Observer;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Psr\Log\LoggerInterface;
use Springbot\Main\Helper\Data as SpringbotHelper;

/**
 * Class CartReconstitutionObserver
 * @package Springbot\Main\Observer
 */
class CartReconstitutionObserver implements ObserverInterface
{
    private $_checkoutSession;
    private $_action;
    private $_messageManager;
    private $_springbotHelper;
    private $_loggerInterface;

    /**
     * @param Action $action
     * @param LoggerInterface $loggerInterface
     * @param ManagerInterface $messageManager
     * @param Session $checkoutSession
     * @param SpringbotHelper $springbotHelper
     */
    public function __construct(
        Action $action,
        ManagerInterface $messageManager,
        Session $checkoutSession,
        SpringbotHelper $springbotHelper,
        LoggerInterface $loggerInterface
    ) {
        $this->_action = $action;
        $this->_checkoutSession = $checkoutSession;
        $this->_loggerInterface = $loggerInterface;
        $this->_messageManager = $messageManager;
        $this->_springbotHelper = $springbotHelper;
    }

    /**
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        try {
            if ($quoteId = $this->_action->getRequest()->getParam('quote_id')) {
                $suppliedSecurityHash = $this->_action->getRequest()->getParam('sec_key');
                $this->_springbotHelper->setQuote($quoteId, $suppliedSecurityHash);
            }
        } catch (LocalizedException $e) {
            $this->_messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_messageManager->addException($e, __('Cart Reconstitution error'));
        }
        return;
    }
}
