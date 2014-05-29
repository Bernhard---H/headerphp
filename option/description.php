<?php
namespace htlwy\headerphp\Option;

use htlwy\headerphp\Navroot;
use htlwy\headerphp\Option;

/**
 * Class Description
 */
class Description extends option
{
    /**
     * The default language is used for websites with a single language
     * and is the defaultvalue of navroot::$default_lang
     *
     * @var array
     */
    protected $_descr = array('default' => '');

    /**
     * @param string|string[] $descr
     */
    public function __construct($descr = null)
    {
        if(isset($descr))
        {
            $this->set($descr);
        }
    }

    /**
     * Sets $_descr. Expects $descr either to be a string (for single
     * language systems) or an array with the language as key.
     *
     * @param string|string[] $descr
     *
     * @throws \InvalidArgumentException
     */
    public function set($descr)
    {
        if(is_string($descr))
        {
            if($descr != '')
            {
                $this->_descr['default'] = $descr;
                $this->_isdefault        = false;
            }
        }
        elseif(is_array($descr))
        {
            foreach($descr as $key => $value)
            {
                $this->_descr[strtolower($key)] = $value;
                $this->_isdefault               = false;
            }
        }
        else
        {
            throw new \InvalidArgumentException(
                    'The description has to be
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
            if(isset($this->_descr[$lang]))
            {
                return $this->_descr[$lang];
            }
            elseif(isset($this->_descr[Navroot::$default_lang]))
            {
                return $this->_descr[Navroot::$default_lang];
            }
            else
            {
                return $this->_descr['default'];
            }
        }
        else
        {
            if(isset($this->_descr[Navroot::$lang]))
            {
                return $this->_descr[Navroot::$lang];
            }
            elseif(isset($this->_descr[Navroot::$default_lang]))
            {
                return $this->_descr[Navroot::$default_lang];
            }
            else
            {
                return $this->_descr['default'];
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
        return $this->_descr;
    }

}
