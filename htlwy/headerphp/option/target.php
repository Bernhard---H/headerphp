<?php
namespace htlwy\headerphp\Option;

use htlwy\headerphp\Option;

/**
 * Class Target
 * manages the value of the target option.
 */
class Target extends Option
{
    /**
     * @var string
     */
    protected $_target = '_self';

    /**
     * @param string $target
     */
    public function __construct($target = null)
    {
        $this->target($target);
    }

    /**
     * @param string|null $target
     * @return string
     */
    public function target($target = null)
    {
        if (isset($target)) {
            $this->_target = $target;
            $this->_isdefault = false;
        } else {
            return $this->_target;
        }
    }
}