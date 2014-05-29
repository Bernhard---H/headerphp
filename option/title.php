<?php
namespace htlwy\Header\Option;

use htlwy\Header\Navroot;
use htlwy\Header\Option;

/**
 * Class Title
 */
class Title extends Option
{
    /**
     * The "default" language is used for websites with a single language
     * and is the defaultvalue of navroot::$default_lang
     *
     * @var array
     */
    protected $_title = array('default' => '');

    /**
     * @param string|array $title
     */
    public function __construct($title = null)
    {
        if (isset($title)) {
            $this->set($title);
        }
    }

    /**
     * Sets $_title. Expects $title either to be a string (for single
     * language systems) or an array with the language as key.
     *
     * @param $title
     *
     * @throws \InvalidArgumentException
     */
    public function set($title)
    {
        if (is_array($title)) {
            foreach ($title as $key => $value) {
                $this->_title[strtolower($key)] = $value;
                $this->_isdefault               = false;
            }
        } elseif ($title != '') {
            $this->_title['default'] = $title;
            $this->_isdefault        = false;
        }
    }

    /**
     * Returns the value for the current aktive language or a default
     * one. If an argument is set, the function returns the value for
     * the passed string
     *
     * @param null $lang
     *
     * @return mixed
     */
    public function get($lang = null)
    {
        if (isset($lang)) {
            if (isset($this->_title[$lang])) {
                return $this->_title[$lang];
            } elseif (isset($this->_title[Navroot::$default_lang])) {
                return $this->_title[Navroot::$default_lang];
            } else {
                return $this->_title['default'];
            }
        } else {
            if (isset($this->_title[Navroot::$lang])) {
                return $this->_title[Navroot::$lang];
            } elseif (isset($this->_title[Navroot::$default_lang])) {
                return $this->_title[Navroot::$default_lang];
            } else {
                return $this->_title['default'];
            }
        }
    }

    /**
     * The function returns an array of all set languages and its values
     * including the default value
     *
     * @return array
     */
    public function get_all()
    {
        return $this->_title;
    }
}