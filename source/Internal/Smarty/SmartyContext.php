<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Smarty;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\UtilsView;
use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContext;

/**
 * Class SmartyContext
 * @package OxidEsales\EshopCommunity\Internal\Smarty
 */
class SmartyContext extends BasicContext implements SmartyContextInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var UtilsView
     */
    private $utilsView;

    /**
     * Context constructor.
     *
     * @param Config    $config
     * @param UtilsView $utilsView
     */
    public function __construct(Config $config, UtilsView $utilsView)
    {
        $this->config = $config;
        $this->utilsView = $utilsView;
    }

    /**TT
     * @return bool
     */
    public function getTemplateEngineDebugMode(): bool
    {
        $debugMode = $this->getConfigParameter('iDebug');
        return ($debugMode == 1 || $debugMode == 3 || $debugMode == 4);
    }

    /**TT
     * @return bool
     */
    public function showTemplateNames(): bool
    {
        $debugMode = $this->getConfigParameter('iDebug');
        return ($debugMode == 8 && !$this->getBackendMode());
    }

    /**TT
     * @return bool
     */
    public function getTemplateSecurityMode(): bool
    {
        return (bool) $this->getDemoShopMode();
    }

    /**TT
     * @return string
     */
    public function getTemplateCompileDirectory(): string
    {
        return $this->utilsView->getSmartyDir();
    }

    /**TT
     * @return array
     */
    public function getTemplateDirectories(): array
    {
        return $this->utilsView->getTemplateDirs();
    }

    /**TT
     * @return string
     */
    public function getTemplateCompileId(): string
    {
        return $this->utilsView->getTemplateCompileId();
    }

    /**TT
     * @return bool
     */
    public function getTemplateCompileCheckMode(): bool
    {
        return (bool) $this->getConfigParameter('blCheckTemplates');
    }

    /**TT
     * @return array
     */
    public function getSmartyPluginDirectories(): array
    {
        return $this->utilsView->getSmartyPluginDirectories();
    }

    /**TT
     * @return int
     */
    public function getTemplatePhpHandlingMode(): int
    {
        return (int) $this->getConfigParameter('iSmartyPhpHandling');
    }

    /**
     * @return string
     */
    public function getShopTemplatePluginDirectory(): string
    {
        $coreDirectory = $this->getConfigParameter('sCoreDir');

        return $coreDirectory . 'Smarty/Plugin';
    }

    /**
     * @param string $templateName
     *
     * @return string
     */
    public function getTemplatePath($templateName): string
    {
        return $this->config->getTemplatePath($templateName, $this->getBackendMode());
    }

    /**
     * @param string $name
     * @return mixed
     */
    private function getConfigParameter($name)
    {
        return $this->config->getConfigParam($name);
    }

    /**
     * @return bool
     */
    private function getBackendMode()
    {
        return $this->config->isAdmin();
    }

    /**
     * @return bool
     */
    private function getDemoShopMode()
    {
        return $this->config->isDemoShop();
    }
}
