<?php
namespace htlwy\headerphp\Option;

use htlwy\headerphp\Option;

/**
 * Class Title
 */
class Title extends Option
{
    /**
     * @var string
     */
    protected $_title = '';

    /**
     * @param string $title
     */
    public function __construct($title = null)
    {
        if (isset($title)) {
            $this->set($title);
        }
    }

    /**
     * @param string $title
     */
    public function set($title)
    {
        $this->_title = $title;
        $this->_isdefault = false;
    }

    /**
     * @return string
     */
    public function get()
    {
        return $this->_title;
    }
}