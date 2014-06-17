<?php
namespace htlwy\headerphp\Option;

use htlwy\headerphp\Option;

/**
 * Class Design
 * define the type of design the site supports
 */
class Design extends Option
{
    /**
     * @var string
     */
    protected $_design = '';

    /**
     * @param string $design
     */
    public function __construct($design = null)
    {
        if(isset($design))
        {
            $this->set($design);
        }
    }

    /**
     * @param string $design
     *
     * @throws \InvalidArgumentException
     */
    public function set($design)
    {
        if(is_string($design))
        {
            $this->_design = strtolower($design);
            $this->_isdefault = false;
        }
        else
        {
            throw new \InvalidArgumentException('The design-value has to be a string!');
        }
    }

    /**
     * Returns the value of $_design
     *
     * @return string
     */
    public function get()
    {
        return $this->_design;
    }
}