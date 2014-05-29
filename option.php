<?php
namespace htlwy\Header;

/**
 * Class Option
 */
class Option
{
    /**
     * If the value is TRUE, some objects get overwritten by options
     * set for parents
     *
     * @var bool
     */
    protected $_isdefault = true;

    /**
     * @return bool
     */
    public function isdefault()
    {
        return $this->_isdefault;
    }
}