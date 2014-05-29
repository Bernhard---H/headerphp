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
        if (is_string($head)) {
            if ($head != '') {
                $this->_head      = $head;
                $this->_isdefault = false;
            }
        } else {
            throw new \InvalidArgumentException('The head has to be a string!');
        }
    }

    /**
     * @return string
     */
    public function get()
    {
        return $this->_head;
    }

}