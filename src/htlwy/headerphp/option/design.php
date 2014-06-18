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
     */
    public function set($design)
    {
        $this->_design = $design;
        $this->_isdefault = false;
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