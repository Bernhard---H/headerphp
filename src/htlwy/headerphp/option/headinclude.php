<?php
namespace htlwy\headerphp\Option;

use htlwy\headerphp\Option;

/**
 * Class Headinclude
 * expects HTML source code that is needs to be added to the head section
 */
class Headinclude extends Option
{
    /**
     * @var string
     */
    protected $_headinclude = '';

    /**
     * @param string $include
     */
    public function __construct($include = null)
    {
        $this->headinclude($include);
    }

    /**
     * @param string $include
     *
     * @return string
     */
    public function headinclude($include = null)
    {
        if (isset($include)) {
            $this->_headinclude = $include;
            $this->_isdefault = false;
        } else {
            return $this->_headinclude;
        }
    }
}