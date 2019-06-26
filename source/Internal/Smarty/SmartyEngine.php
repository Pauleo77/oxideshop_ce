<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Smarty;

/**
 * Class SmartyEngine
 * @package OxidEsales\EshopCommunity\Internal\Smarty
 */
class SmartyEngine
{
    /**
     * @var string
     */
    private $cacheId;

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
     * @param array  $parameters An array of parameters to pass to the template
     *
     * @return string The evaluated template as a string
     */
    public function render(string $name, array $parameters = [])
    {
        foreach ($parameters as $key => $value) {
            $this->engine->assign($key, $value);
        }
        if (isset($this->cacheId)) {
            return $this->engine->fetch($name, $this->cacheId);
        }
        return $this->engine->fetch($name);
    }

    /**
     * Renders a fragment of the template.
     *
     * @param string $fragment   The template fragment to render
     * @param array  $parameters An array of parameters to pass to the template
     *
     * @return string The evaluated template as a string
     */
    public function renderFragment(string $fragment, array $parameters = [])
    {
        $this->engine->force_compile = true;
        foreach ($parameters as $key => $value) {
            $this->engine->assign($key, $value);
        }
        $this->engine->oxidcache = new \OxidEsales\Eshop\Core\Field($fragment, \OxidEsales\Eshop\Core\Field::T_RAW);
        return $this->engine->fetch($this->cacheId);
    }

    /**
     * Returns true if the template exists.
     *
     * @param string $name A template name
     *
     * @return bool true if the template exists, false otherwise
     */
    public function exists($name)
    {
        return $this->engine->template_exists($name);
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function addGlobal($name, $value)
    {
        $this->globals[$name] = $value;
        $this->engine->assign($name, $value);
    }

    /**
     * Returns assigned globals.
     *
     * @return array
     */
    public function getGlobals()
    {
        return $this->globals;
    }

    /**
     * @param string $cacheId
     */
    public function setCacheId($cacheId)
    {
        $this->cacheId = $cacheId;
    }

    /**
     * Returns the template file extension.
     *
     * @return string
     */
    public function getDefaultFileExtension()
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
        $this->engine->$name = $value;
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
