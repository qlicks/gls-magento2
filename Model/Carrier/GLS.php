<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */

namespace TIG\GLS\Model\Carrier;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\ResultFactory;
use Psr\Log\LoggerInterface;
use TIG\GLS\Model\Config\Provider\Account;

class GLS extends AbstractCarrier implements CarrierInterface
{
    const GLS_CARRIER_METHOD = 'tig_gls';

    // @codingStandardsIgnoreLine
    protected $_code = 'tig_gls';

    /** @var Account $accountConfigProvider */
    private $accountConfigProvider;

    /** @var ResultFactory $rateResultFactory */
    private $rateResultFactory;

    /** @var MethodFactory $rateMethodFactory */
    private $rateMethodFactory;

    /** @var RateRequest $request */
    private $request;

    /** @var ScopeConfigInterface|\TIG\GLS\Model\Carrier\ScopeConfigInterface */
    private $scopeConfig;

    /**
     * GLS constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory         $rateErrorFactory
     * @param LoggerInterface      $logger
     * @param Account              $accountConfigProvider
     * @param ResultFactory        $rateResultFactory
     * @param MethodFactory        $rateMethodFactory
     * @param array                $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        Account $accountConfigProvider,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);

        $this->accountConfigProvider = $accountConfigProvider;
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Collect and get rates
     *
     * @param RateRequest $request
     *
     * @return \Magento\Framework\DataObject|bool|null|Result
     * @api
     */
    // @codingStandardsIgnoreLine
    public function collectRates(RateRequest $request)
    {
        if (!$this->accountConfigProvider->isValidatedSuccesfully()) {
            return false;
        }

        if (!$this->getConfigFlag('active')) {
            return false;
        }

        /** @var RateRequest $request */
        $this->request = $request;

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->rateResultFactory->create();

        $method = $this->getMethod();

        $result->append($method);

        return $result;
    }

    /**
     * @return Method
     */
    private function getMethod()
    {
        /** @var Method $method */
        $method = $this->rateMethodFactory->create();
        $amount = $this->getShippingPrice();

        $method->setCarrier(self::GLS_CARRIER_METHOD);
        $method->setCarrierTitle($this->getConfigData('title'));
        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData('name'));
        $method->setCost($amount);
        $method->setPrice($amount);

        return $method;
    }

    /**
     * @return float
     */
    private function getShippingPrice()
    {
        $configPrice = $this->getConfigData('price');

        $countryOfOrigin = $this->getCountryOfOrigin();

        if ($this->request->getDestCountryId() != $countryOfOrigin) {
            $configPriceInternational = $this->getConfigData('international_handling_fee');
            $configPrice += $configPriceInternational;
        }

        $shippingPrice = $this->getFinalPriceWithHandlingFee($configPrice);

        return $shippingPrice;
    }

    /**
     *
     * @return mixed
     */
    public function getCountryOfOrigin()
    {
        $country = $this->scopeConfig->getValue('general/store_information/country_id',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $country ?: 'NL';
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     * @api
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }
}
