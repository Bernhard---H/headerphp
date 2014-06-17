<?php
namespace htlwy\headerphp\Option;

use htlwy\headerphp\Option;

/**
 * Class Titlebar
 * Wird über der Kopf-Menüleiste angezeigt.
 */
class Titlebar extends Option
{
    /**
     * @var string
     */
    protected $_head = '';

    /**
     * @param string $head
     */
    public function __construct($head = null)
    {
        if (isset($head)) {
            $this->set($head);
        }
    }

    /**
     * @param $head
     *
     * @throws \InvalidArgumentException
     */
    public function set($head)
    {
        $this->_head = $head;
        $this->_isdefault = false;
    }

    /**
     * @return string
     */
    public function get()
    {
        return $this->_head;
    }

}