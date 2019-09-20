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

namespace TIG\GLS\Api\Shipment;

use Magento\Framework\Api\SearchCriteriaInterface;
use TIG\GLS\Api\Shipment\Data\LabelInterface;

/**
 * Do not remove the fully classified path names. This is needed for the API
 * extension later on!
 *
 * Interface LabelRepositoryInterface
 * @package TIG\GLS\Api\Shipment
 */
interface LabelRepositoryInterface
{
    /**
     * @param int $id
     *
     * @return \TIG\GLS\Api\Shipment\Data\LabelInterface;
     */
    public function getById($id);

    /**
     * @param int $shipmentId
     *
     * @return \TIG\Gls\Api\Shipment\Data\LabelInterface;
     */
    public function getByShipmentId($shipmentId);

    /**
     * @param \TIG\GLS\Api\Shipment\Data\LabelInterface $label
     *
     * @return \TIG\GLS\Api\Shipment\Data\LabelInterface
     */
    public function save(LabelInterface $label);

    /**
     * @param \TIG\GLS\Api\Shipment\Data\LabelInterface $label
     *
     * @return \TIG\GLS\Api\Shipment\Data\LabelInterface
     */
    public function delete(LabelInterface $label);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \TIG\GLS\Api\Shipment\Data\LabelSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}