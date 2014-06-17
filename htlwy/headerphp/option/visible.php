<?php
namespace htlwy\headerphp\Option;

use htlwy\headerphp\Option;

/**
 * Class Visible
 */
class Visible extends Option
{
    /**
     * $unset contains the value that the isvisible(); function will return,
     * if no status is defined.
     *
     * @var bool
     */
    public static $unset = true;

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

    /**
     * @var string[]
     */
    protected $_visible = array();
    /**
     * @var string[]
     */
    protected $_nvisible = array();

    /**
     * @param bool|string|array $visible
     */
    public function __construct($visible = array(null))
    {
        if(func_num_args() == 0)
        {
            $this->_visible    = array();
            $this->_nvisible   = array();
            $this->_visible[0] = 'all';
        }
        else
        {
            $args = func_num_args();
            for($i = 0; $i < $args; $i++)
            {
                $this->visibility(func_get_arg($i));
            }
        }
    }

    /**
     * The function expects a list of strings, setting their visibility-
     * status to TRUE. To negate the status, add an exclamation mark as
     * first character.
     * Are states set twice, negated options are preferred!
     *
     * @param array $visible
     *
     * @throws \InvalidArgumentException
     */
    public function visibility($visible = array())
    {
        $args = func_num_args();
        for($i = 0; $i < $args; $i++)
        {
            $arg = func_get_arg($i);

            if(is_bool($arg))
            {
                if($arg)
                    // The function will always return the set value
                    // A bool value will overwrite all existing option
                {
                    $this->_visible    = array();
                    $this->_nvisible   = array();
                    $this->_visible[0] = 'all';
                }
                else
                {
                    $this->_visible     = array();
                    $this->_nvisible    = array();
                    $this->_nvisible[0] = 'all';
                }
                $this->_isdefault = false;
            }
            elseif(is_string($arg))
            {
                $arg = trim(strtolower($arg));

                if(substr($arg, 0, 1) == '!')
                {
                    $arg = substr_replace($arg, '', 0, 1);

                    $this->_nvisible[count($this->_nvisible)] = $arg;
                }
                else
                {
                    $this->_visible[count($this->_visible)] = $arg;
                }

                $this->_isdefault = false;
            }
            else
            {
                throw new \InvalidArgumentException('The visibility has to be a string!');
            }
        }
    }

    /**
     * Checks if the passed string is marked as FALSE or TRUE
     * If the string was not found, the function returns the defined value.
     * If $visible is an array the function will search for FALSE and
     * return TRUE if none was found.
     *
     * @param $visible
     *
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function isvisible($visible)
    {
        if(is_string($visible))
        {
            if(array_search($visible, $this->_nvisible) !== false ||
               array_search('all', $this->_nvisible) !== false
            )
            {
                return false;
            }
            elseif(array_search($visible, $this->_visible) !== false ||
                   array_search('all', $this->_visible) !== false
            )
            {
                return true;
            }
            else
            {
                return visible::$unset;
            }
        }
        elseif(is_array($visible))
        {
            foreach($visible as $value)
            {
                if(array_search($value, $this->_nvisible) !== false ||
                   array_search('all', $this->_nvisible) !== false
                )
                {
                    $ret = false;
                }
                elseif(array_search($value, $this->_visible) !== false ||
                       array_search('all', $this->_visible) !== false
                )
                {
                    $ret = true;
                }
                else
                {
                    $ret = visible::$unset;
                }
                if($ret == false)
                {
                    return false;
                }
            }
            return true;
        }
        else
        {
            throw new \InvalidArgumentException('The visibility has to be a string!');
        }
    }
}