<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Smarty;

/**
 * Class SmartyEngineConfiguration
 * @package OxidEsales\EshopCommunity\Internal\Smarty
 */
interface SmartyConfigurationFactoryInterface
{
    /**
     * @return array
     */
    public function getConfiguration();
}
