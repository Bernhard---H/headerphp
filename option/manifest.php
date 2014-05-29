<?php
namespace htlwy\Header\Option;

use htlwy\Header\Option;

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
     * @param $manifest
     *
     * @throws \InvalidArgumentException
     */
    public function set($manifest)
    {
        if (is_string($manifest)) {
            $this->_manifest  = $manifest;
            $this->_isdefault = false;
        } else {
            throw new \InvalidArgumentException('The manifest-value has to be a string!');
        }
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