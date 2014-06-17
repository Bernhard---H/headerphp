<?php
namespace htlwy\headerphp\Option;

use htlwy\headerphp\Navroot;
use htlwy\headerphp\Option;

/**
 * Class author
 */
class author extends Option
{
    /**
     * The default language is used for websites with a single language
     * and is the defaultvalue of navroot::$default_lang
     *
     * @var array
     */
    protected $_author = array('default' => '');

    /**
     * @param string|string[] $author
     */
    public function __construct($author = null)
    {
        if (isset($author)) {
            $this->set($author);
        }
    }

    /**
     * Sets $_author. Expects $author either to be a string (for single
     * language systems) or an array with the language as key.
     *
     * @param string|string[] $author
     *
     * @throws \InvalidArgumentException
     */
    public function set($author)
    {
        if (is_string($author)) {
            if ($author != '') {
                $this->_author['default'] = $author;
                $this->_isdefault         = false;
            }
        } elseif (is_array($author)) {
            foreach ($author as $key => $value) {
                $this->_author[strtolower($key)] = $value;
                $this->_isdefault                = false;
            }
        } else {
            throw new \InvalidArgumentException(
                    'The description has to be
                                    a string or an array!'
            );
        }
    }

    /**
     * Returns the value for the current aktive language or a default
     * one. If an argument is set, the function returns the value for
     * the passed string
     *
     * @param string $lang
     *
     * @return mixed
     */
    public function get($lang = null)
    {
        if (isset($lang)) {
            if (isset($this->_author[$lang])) {
                return $this->_author[$lang];
            } elseif (isset($this->_author[Navroot::$default_lang])) {
                return $this->_author[Navroot::$default_lang];
            } else {
                return $this->_author['default'];
            }
        } else {
            if (isset($this->_author[Navroot::$lang])) {
                return $this->_author[Navroot::$lang];
            } elseif (isset($this->_author[Navroot::$default_lang])) {
                return $this->_author[Navroot::$default_lang];
            } else {
                return $this->_author['default'];
            }
        }
    }

    /**
     * The function returns an array of all set languages and its values
     * including the default value
     *
     * @return string[]
     */
    public function get_all()
    {
        return $this->_author;
    }

}