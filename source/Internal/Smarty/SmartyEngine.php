<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Smarty;

use OxidEsales\EshopCommunity\Internal\Templating\TemplateEngineInterface;

/**
 * Class SmartyEngine
 * @package OxidEsales\EshopCommunity\Internal\Smarty
 */
class SmartyEngine implements TemplateEngineInterface
{
    /**
     * The template engine.
     *
     * @var \Smarty
     */
    private $engine;

    /**
     * Array of global parameters
     *
     * @var array
     */
    private $globals = [];

    /**
     * Constructor.
     *
     * @param \Smarty $engine
     */
    public function __construct(\Smarty $engine)
    {
        $this->engine = $engine;
    }

    /**
     * Renders a template.
     *
     * @param string $name       A template name
     * @param array  $context An array of parameters to pass to the template
     *
     * @return string The evaluated template as a string
     */
    public function render(string $name, array $context = []): string
    {
        foreach ($context as $key => $value) {
            $this->engine->assign($key, $value);
        }
        if (isset($context['oxEngineTemplateId'])) {
            return $this->engine->fetch($name, $context['oxEngineTemplateId']);
        }
        return $this->engine->fetch($name);
    }

    /**
     * Renders a fragment of the template.
     *
     * @param string $fragment   The template fragment to render
     * @param string $fragmentId The Id of the fragment
     * @param array  $context    An array of parameters to pass to the template
     *
     * @return string
     */
    public function renderFragment(string $fragment, string $fragmentId, array $context = []): string
    {
        // save old tpl data
        $tplVars = $this->engine->_tpl_vars;
        $forceRecompile = $this->engine->force_compile;
        $this->engine->force_compile = true;
        foreach ($context as $key => $value) {
            $this->engine->assign($key, $value);
        }
        $this->engine->oxidcache = new \OxidEsales\Eshop\Core\Field($fragment, \OxidEsales\Eshop\Core\Field::T_RAW);
        $result =  $this->engine->fetch($fragmentId);
        $this->engine->_tpl_vars = $tplVars;
        $this->engine->force_compile = $forceRecompile;
        return $result;
    }

    /**
     * Returns true if the template exists.
     *
     * @param string $name A template name
     *
     * @return bool true if the template exists, false otherwise
     */
    public function exists(string $name): bool
    {
        return $this->engine->template_exists($name);
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function addGlobal(string $name, $value)
    {
        $this->globals[$name] = $value;
        $this->engine->assign($name, $value);
    }

    /**
     * Returns assigned globals.
     *
     * @return array
     */
    public function getGlobals(): array
    {
        return $this->globals;
    }

    /**
     * Returns the template file extension.
     *
     * @return string
     */
    public function getDefaultFileExtension(): string
    {
        return 'tpl';
    }

    /**
     * Pass parameters to the Smarty instance.
     *
     * @param string $name  The name of the parameter.
     * @param mixed  $value The value of the parameter.
     */
    public function __set($name, $value)
    {
        if (property_exists($this->engine, $name)) {
            $this->engine->$name = $value;
        }
    }

    /**
     * Pass parameters to the Smarty instance.
     *
     * @param string $name The name of the parameter.
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->engine->$name;
    }
}
