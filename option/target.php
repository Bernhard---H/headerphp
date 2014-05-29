<?php
namespace htlwy\Header\Option;

use htlwy\Header\Option;

/**
 * Class Target
 * manages the value of the target option. By default only the four
 * in HTML predefined arguments are allowed. To allow any target
 * the static attribute target::$check_target has to be FALSE.
 */
class Target extends Option
{
    /**
     * On TRUE, only '_blank', '_self', '_top' and '_parent' are allowed.
     * Set this to FALSE if you want to use HTML4-Frames
     *
     * @var bool
     */
    public static $check_target = true;

    /**
     * Set the default targe for links leading to:
     * ... the navroot::$home directory
     *
     * @var string
     */
    public static $target_home = '_self';

    /**
     * ... the servers document root directory
     *
     * @var string
     */
    public static $target_docroot = '_self';

    /**
     * ... other link e.g. other webpages
     *
     * @var string
     */
    public static $target_others = '_blank';

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    /**
     * @var string
     */
    protected $_target = '_self';

    /**
     * @param string $target
     */
    public function __construct($target = null)
    {
        $this->target($target);
    }

    public function target($target = null)
    {
        if(isset($target))
        {
            if(target::$check_target)
                // Default value is TRUE
            {
                switch($target)
                {
                    case '_blank':
                        $this->_target = $target;
                        break;
                    case '_self':
                        $this->_target = $target;
                        break;
                    case '_parent':
                        $this->_target = $target;
                        break;
                    case '_top':
                        $this->_target = $target;
                        break;

                    default:
                        throw new \InvalidArgumentException(
                                'The passed value for
                                                            target seems to be no target predefined in the HTML -
                                                            standard. To use other values for target, set the
                                                            value of the static attribute target::$check_target to FALSE.');
                }
                $this->_isdefault = false;
            }
            else
                // recommended for working with frames
            {
                if(is_string($target))
                {
                    $this->_target    = $target;
                    $this->_isdefault = false;
                }
                else
                {
                    throw new \InvalidArgumentException('The target has to be a string!');
                }
            }
        }
        else
        {
            return $this->_target;
        }
    }
}