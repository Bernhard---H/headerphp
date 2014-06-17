<?php
namespace htlwy\headerphp\Option;

use htlwy\headerphp\Navroot;
use htlwy\headerphp\Option;

/**
 * Class Keywords
 */
class Keywords extends Option
{
    /**
     * The default language is used for websites with a single language
     * and is the defaultvalue of navroot::$default_lang
     *
     * @var array
     */
    protected $_keywords = array('default' => '');

    /**
     * @param string|string[] $keywords
     */
    public function __construct($keywords = null)
    {
        if(isset($keywords))
        {
            $this->set($keywords);
        }
    }

    /**
     * Sets $_keywords. Expects $keywords either to be a string (for single
     * language systems) or an array with the language as key.
     *
     * @param string|string[] $keywords
     *
     * @throws \InvalidArgumentException
     */
    public function set($keywords)
    {
        if(is_string($keywords))
        {
            if($keywords != '')
            {
                $this->_keywords['default'] = $keywords;
                $this->_isdefault           = false;
            }
        }
        elseif(is_array($keywords))
        {
            foreach($keywords as $key => $value)
            {
                $this->_keywords[strtolower($key)] = $value;
                $this->_isdefault                  = false;
            }
        }
        else
        {
            throw new \InvalidArgumentException(
                    'The title has to be
                                    a string or an array!');
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
        if(isset($lang))
        {
            if(isset($this->_keywords[$lang]))
            {
                return $this->_keywords[$lang];
            }
            elseif(isset($this->_keywords[Navroot::$default_lang]))
            {
                return $this->_keywords[Navroot::$default_lang];
            }
            else
            {
                return $this->_keywords['default'];
            }
        }
        else
        {
            if(isset($this->_keywords[Navroot::$lang]))
            {
                return $this->_keywords[Navroot::$lang];
            }
            elseif(isset($this->_keywords[Navroot::$default_lang]))
            {
                return $this->_keywords[Navroot::$default_lang];
            }
            else
            {
                return $this->_keywords['default'];
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
        return $this->_keywords;
    }
}
