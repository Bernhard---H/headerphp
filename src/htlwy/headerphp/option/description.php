<?php
namespace htlwy\headerphp\Option;

use htlwy\headerphp\Option;

/**
 * Class Description
 */
class Description extends option
{
    /**
     * @var string
     */
    protected $_descr = '';

    /**
     * @param string $descr
     */
    public function __construct($descr = null)
    {
        if (isset($descr)) {
            $this->set($descr);
        }
    }

    /**
     * @param string $descr
     */
    public function set($descr)
    {
        $this->_descr = $descr;
        $this->_isdefault = false;
    }

    /**
     * @return string
     */
    public function get()
    {
        return $this->_descr;
    }
}
