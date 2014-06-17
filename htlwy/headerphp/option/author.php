<?php
namespace htlwy\headerphp\Option;

use htlwy\headerphp\Option;

/**
 * Class author
 */
class author extends Option
{
    /**
     * @var string
     */
    protected $_author = '';

    /**
     * @param string $author
     */
    public function __construct($author = null)
    {
        if (isset($author)) {
            $this->set($author);
        }
    }

    /**
     * @param string $author
     */
    public function set($author)
    {
        $this->_author = $author;
        $this->_isdefault = false;
    }

    /**
     * @return string
     */
    public function get()
    {
        return $this->_author;
    }
}