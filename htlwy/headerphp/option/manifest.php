<?php
namespace htlwy\headerphp\Option;

use htlwy\headerphp\Option;

/**
 * Class Manifest
 * path to the manifest file for offline caching
 */
class Manifest extends Option
{
    /**
     * @var string
     */
    protected $_manifest = '';

    /**
     * @param string $manifest
     */
    public function __construct($manifest = null)
    {
        if (isset($manifest)) {
            $this->set($manifest);
        }
    }

    /**
     * path or url for the html-manifest-attribute
     *
     * @param string $manifest
     */
    public function set($manifest)
    {
            $this->_manifest  = $manifest;
            $this->_isdefault = false;
    }

    /**
     * Returns the value of $_manifest
     *
     * @return string
     */
    public function get()
    {
        return $this->_manifest;
    }
}