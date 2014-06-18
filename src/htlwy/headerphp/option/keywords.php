<?php
namespace htlwy\headerphp\Option;

use htlwy\headerphp\Option;

/**
 * Class Keywords
 */
class Keywords extends Option
{
    /**
     * @var string
     */
    protected $_keywords = '';

    /**
     * @param string $keywords
     */
    public function __construct($keywords = null)
    {
        if (isset($keywords)) {
            $this->set($keywords);
        }
    }

    /**
     * @param string $keywords
     */
    public function set($keywords)
    {
        $this->_keywords = $keywords;
        $this->_isdefault = false;

    }

    /**
     * @return string
     */
    public function get()
    {
        return $this->_keywords;
    }
}
