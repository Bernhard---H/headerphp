<?php
namespace htlwy\headerphp;

use htlwy\headerphp\Option\Target;
use htlwy\headerphp\Option\Title;
use htlwy\headerphp\Option\Visible;

/**
 * Class nav: Basic element, each instance represents an entry in the menu
 */
class Nav implements \ArrayAccess, \Iterator
{
    /**
     * is the path to the file;
     * it assumes that the file is in $_SERVER['DOCUMENT_ROOT']
     * if the string does not start with a slash, the functiion will
     * first try to find the path in the NAV_DEFAULT_ROOT directory
     *
     * @param null $path
     *
     * @return null|string
     * @throws \InvalidArgumentException
     */
    private static $SERVER_NAME = '';
    /**
     * represents the value the user will see
     * (hardcodierte option)
     *
     * @var string
     */
    protected $_label = '';
    /**
     * is the path/link to the file
     * (hardcodierte option)
     *
     * @var string
     */
    protected $_path = '';
    /**
     * caches the returnvalue of the function isactive()
     *
     * @var bool
     */
    protected $_isactive = null;
    /**
     * caches the returnvalue of the function istheactive()
     *
     * @var bool
     */
    protected $_istheactive = null;
    /**
     * addition options:
     * open link in new window, do not show the link in the sitemap
     *
     * @var option[]
     */
    protected $option = array();
    /**
     * instances of this class
     *
     * @var nav[]
     */
    protected $_subnavs = array();

    //== public functions ============================================
    //
    /**
     * needed to implement the Iterator-Interface
     *
     * @var int
     */
    private $_iterator = 0;

    /**
     * Schematischer Aufbau:
     * <code>
     *      new nav([string $label [, string $path [, string $title]][, ...]]);
     * </code>
     *
     * @param mixed $properties
     */
    public function __construct($properties = array())
    {
        $arguments = func_num_args();

        if ($arguments >= 1) {
            $this->label(func_get_arg(0));
            $this->option(new Title(func_get_arg(0)));

            $strings = 1;
            for ($i = 1; $i < $arguments; $i++) {
                $property = func_get_arg($i);
                if (isset($property)) {
                    if (is_string($property)) {
                        if ($strings == 1) // the second string interpreted as path of the associated file
                        {
                            $this->path($property);
                        } elseif ($strings == 2) // the third string interpreted as title of the associated file
                        {
                            $this->option(new Title($property));
                        }
                        $strings++;
                    } elseif ($property instanceof self) {
                        $this->add($property);
                    } elseif ($property instanceof Option) {
                        $this->option($property);
                    }
                }
            }
        }
    }

    /**
     * option() expects an object of one of the option-classes
     *
     * @param Option $option
     */
    public function option(Option $option)
    {
        $this->option[get_class($option)] = $option;
    }

    /**
     * Get or Set the value of $_label
     * Label is the value that the user will see
     * (hardcodierte option)
     *
     * @param null $label
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function label($label = null)
    {
        if (isset($label)) {
            // if an argument is set, the function expects it to be a string
            // otherwise an exception will be thrown
            $this->_label = $label;
        } else {
            // if no argument is set, the function returns the value of $_label
            return $this->_label;
        }
    }

    public function path($path = null)
    {
        if (isset($path)) {
            if (self::$SERVER_NAME == '') {
                self::$SERVER_NAME = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'www.htlwy.ac.at';
            }

            $url = parse_url($path);

            if (isset($url['host']) && $url['host'] != self::$SERVER_NAME) // external links
            {
                $this->_path = self::http_build_url($url);
                $this->option(new Target(Target::$target_others));
            } else {
                if (substr($url['path'], 0, 1) != '/') {
                    $url['path'] = navroot::HOME.'/'.$url['path'];
                }
                $url['host'] = self::$SERVER_NAME;
                if (version_compare(PHP_VERSION, '5.4.7') < 0) {
                    // parse_url() interpretiert URLs erst ab 5.4.7 auch ohne Protokol
                    if (isset($_SERVER['HTTPS'])) {
                        $url['scheme'] = 'https';
                    } else {
                        $url['scheme'] = 'http';
                    }
                }
                $this->_path = self::http_build_url($url);
            }
        } else {
            // if no argument is set, the function returns the value of $_path
            if ($this->_path != "") {
                return $this->_path;
            } else {
                // if no path is set, the function tries to find one in its
                // sub elements and returns the first found path
                $path = '';
                $num_subnavs = count($this->_subnavs);
                $i = 0;
                while ($path == "" && $i < $num_subnavs) {
                    $path = $this->_subnavs[$i]->path();
                    $i++;
                }
                return $path;
            }
        }
    }

    /**
     * http_build_url is a replica of the one in the http-functions
     *
     * @param $url
     *
     * @return string
     */
    protected static function http_build_url($url)
    {
        $ret = '';
        $protocol = false;
        if (isset($url['scheme'])) {
            $ret .= $url['scheme'].'://';
            $protocol = true;
        }
        if (isset($url['host'])) {
            if (!$protocol) {
                $ret .= '//';
            }
            $ret .= $url['host'];
        }
        if (isset($url['path'])) {
            $ret .= $url['path'];
        }
        if (isset($url['query'])) {
            $ret .= '?'.$url['query'];
        }
        if (isset($url['fragment'])) {
            $ret .= '#'.$url['fragment'];
        }

        return $ret;
    }

    /**
     * @param self $object
     */
    public function add(self $object)
    {
        $this->_subnavs[] = $object;
    }

    /**
     * Returns a simple navigation built with an HTML list
     *
     * @param string $visible will be the parameter of the isvisible(); method
     *                         of the visible class
     * @param int $from navigation will start with a subnav; 0 is this one
     * @param int $to defines where to stop adding sub elements
     * @param bool $noactive function will ignore the status of activ e.g.: Sitemap
     *
     * @return string
     */
    public function navigation($visible = 'all', $from = 0, $to = -1, $noactive = false)
    {
        $ret = '';

        if (!isset($this->option[__NAMESPACE__.'\\Option\\Visible']) ||
            $this->option[__NAMESPACE__.'\\Option\\Visible']->isvisible($visible)
        ) {
            if ($from <= 0) {
                $ret .= "<li><a ";
                if ($this->istheactive()) {
                    $ret .= 'class="'.navroot::$nav_theactiv_class.'" ';
                } elseif ($this->isactive()) {
                    $ret .= 'class="'.navroot::$nav_activ_class.'" ';
                }
                $ret .= 'href="'.htmlentities($this->path()).'" target="'.$this->getoption('target')
                        ->target().'" >'.$this->_label.
                    "</a>";
            }

            $from--;
            $to--;
            // START list sub nav items
            if (($this->isactive() || $noactive) && $to != 0) {
                $first = true;
                foreach ($this->_subnavs as $subnav) {
                    $subret = $subnav->navigation($visible, $from, $to, $noactive);
                    if ($subret != '') {
                        if ($first) {
                            if ($from <= -1) {
                                $ret .= "\n<ul>\n";
                            }
                            $first = false;
                        }
                        $ret .= $subret;
                    }
                }
                if (!$first) {
                    if ($from <= -1) {
                        $ret .= "</ul>\n";
                    }
                }
            }
            // END sub nav
            if ($from <= -1) {
                $ret .= "</li>\n";
            }
        }
        return $ret;
    }

    /**
     * Checks, if this is the activ element
     *
     * @return null
     */
    public
    function istheactive()
    {
        if (isset($this->_istheactive)) {
            return $this->_istheactive;
        } else {
            $this->isactive();
            return $this->_istheactive;
        }
    }

    /**
     * Checks if this or a sub nav element was requested by the browser
     *
     * @return bool
     */
    public
    function isactive()
    {
        if (isset($this->_isactive)) {
            return $this->_isactive;
        } else {
            $active = true;
            if (parse_url($this->_path, PHP_URL_SCHEME) == 'https') {
                if (!isset($_SERVER['HTTPS'])) {
                    $active = false;
                    //echo '<br />false: '.$this->_path;
                }
            }

            $servername = parse_url($this->_path, PHP_URL_HOST);
            if ($active && !((isset($_SERVER['SERVER_NAME']) && $servername == $_SERVER['SERVER_NAME']) ||
                    $servername == '')
            ) {
                $active = false;
                //echo '<br />false: '.$this->_path;
            }

            if ($active && parse_url($this->_path, PHP_URL_PATH) != $_SERVER['PHP_SELF']) {
                $active = false;
                //echo '<br />false: '.$this->_path;
            }

            // check the GET requests
            // start the key with ! to exclude it
            if ($active && $query = parse_url($this->_path, PHP_URL_QUERY)) {
                $get = array();
                $nget = array();
                //echo '<br />false?: '.$this->_path;

                $expquery = explode('&', $query);

                foreach ($expquery as $value) {
                    $exp = explode('=', $value);

                    if (!isset($exp[1])) {
                        $exp[1] = '';
                    }

                    if (substr($exp[0], 0, 1) == '!') {
                        // remove ! and save to the array
                        $nget[substr($exp[0], 1)] = $exp[1];

                        // remove the NOT-Parameter from the path
                        if (strpos($query, $exp[0].'='.$exp[1]) == 0) {
                            $this->_path = str_ireplace('?'.$exp[0].'='.$exp[1], '', $this->_path);
                        } else {
                            $this->_path = str_ireplace('&'.$exp[0].'='.$exp[1], '', $this->_path);
                        }
                    } else {
                        $get[$exp[0]] = $exp[1];
                    }
                }

                foreach ($get as $key => $value) {
                    if (!isset($_GET[$key])) {
                        //echo 'Key: '.$key;
                        $active = false;
                        break;
                    } elseif ($value != '' && $_GET[$key] != $value) {
                        //echo 'Value: '.$value;
                        $active = false;
                        break;
                    }
                    //echo '<br />Key: '.$key; echo ' Value: '.$value;
                }

                if ($active) {
                    foreach ($nget as $key => $value) {
                        if (isset($_GET[$key])) {
                            if ($value == '' || $_GET[$key] == $value) {
                                //echo 'Key: '.$key;
                                $active = false;
                                break;
                            }
                        }
                        //echo '<br />Key: '.$key; echo ' Value: '.$value;
                    }
                }

            }
            // END check get

            $this->_isactive = $active;
            $this->_istheactive = $active;

            // If this is not active, the functions checks the sub elements
            if (!$this->_isactive) {
                foreach ($this->_subnavs as $value) {
                    if ($value->isactive()) {
                        $this->_isactive = true;
                        if (!navroot::$multi_active) {
                            break;
                        }
                    }
                }
            }
            return $this->_isactive;
        }
    }

    /**
     * @param string $option
     *
     * @return option|option[]|bool
     */
    public
    function getoption(
        $option = null
    ) {
        // alle zurueck geben, wenn keine spezielle gesetzt ist
        if (!isset($option)) {
            return $this->option;
        }

        $option = __NAMESPACE__.'\\Option\\'.$option;
        if (isset($this->option[$option])) {
            return $this->option[$option];
        } elseif (is_subclass_of($option, __NAMESPACE__.'\\Option')) {
            return new $option;
        } else {
            return false;
        }
    }

    /**
     * Returns an array of <a></a> tags with the links of the navigation
     *
     * @param string $visible will be the parameter of the isvisible(); method
     *                           of the visible class
     * @param int $from navigation will start with a subnav; 0 is this one
     * @param int $to defines where to stop adding sub elements
     * @param bool $activeonly function will return only links form the active ones
     *
     * @return array
     */
    public
    function navlinks(
        $visible = 'all',
        $from = 0,
        $to = -1,
        $activeonly = false
    ) {
        $ret = array(0 => '');

        if (isset($this->option[__NAMESPACE__.'\\Option\\Visible']) &&
            $this->option[__NAMESPACE__.'\\Option\\Visible']->isvisible($visible)
        ) {
            if ($from <= 0 && (!$activeonly || ($this->isactive()))) {
                $ret[0] = "<a ";
                if ($this->istheactive()) {
                    $ret[0] .= 'class="'.navroot::$nav_theactiv_class.'" ';
                } elseif ($this->isactive()) {
                    $ret[0] .= 'class="'.navroot::$nav_activ_class.'" ';
                }
                $ret[0] .= 'href="'.$this->path().'" target="'.$this->getoption('target')
                        ->target().'" >'.$this->_label."</a>";
            }

            $from--;
            $to--;
            // START list sub nav items
            if (($this->isactive()) && $to != 0) {
                $i = 0;
                foreach ($this->_subnavs as $subnav) {
                    $subret = $subnav->navlinks($visible, $from, $to, $activeonly);
                    if ($subret[0] != '') {
                        $ret[1][$i] = $subret[0];
                        $i++;
                    }
                    if (isset($subret[1])) {
                        $ret[1][$i] = $subret[1];
                        $i++;
                    }
                }
            }
        }
        return $ret;
    }

    /**
     * Returns a simple navigation built with an HTML list
     *
     * @param string $visible will be the parameter of the isvisible(); method
     *                         of the visible class
     *
     * @return string
     */
    public
    function xmlsitemap(
        $visible = 'all'
    ) {
        $ret = '';
        if (isset($this->option[__NAMESPACE__.'\\Option\\Visible']) &&
            $this->option[__NAMESPACE__.'\\Option\\Visible']->isvisible($visible)
        ) {
            $ret .= "\t<url>
        <loc>".htmlentities($this->path())."</loc>
    </url>\n\n";

            // START list sub nav items
            foreach ($this->_subnavs as $subnav) {
                $ret .= $subnav->xmlsitemap($visible);
            }
        }
        return $ret;
    }

    /**
     * trys to find the option of the activ element
     *
     * @param      $option
     * @param bool $all when `true` the method will return an arry with all
     *                  obejcts of the activ navs.
     *
     * @return option|option[]|bool
     */
    public
    function getactiveoption(
        $option,
        $all = false
    ) {
        // is this element marked as active?
        if ($this->isactive()) {
            $return = false;

            // is this element marked as theactive?
            if ($this->_istheactive) {
                $return = true;
            } else {
                // => one of the childes is active
                // search in childes for active options
                foreach ($this->_subnavs as $subnav) {
                    $tmp = $subnav->getactiveoption($option, $all);
                    if ($tmp !== false) {
                        $return = $tmp;
                        break;
                    }
                }
            }

            // Soll ein Objekt/True zurückgegeben werden?
            if ($return !== false) {
                // Wird ein Array erwartet?
                if ($all) {
                    if (is_array($return) && is_object($return[0]) && !$return[0]->isdefault()) {
                        // $return enthält ein Array von nicht Default-Objekten
                        array_unshift($return, $this->option[$option]);
                        return $return;
                    } elseif (isset($this->option[$option])) {
                        // $return ist kein Array (nur TRUE) ODER
                        // das erste Element ist kein Objekt (z.B. TRUE) ODER
                        // das erste (und einzige) Objekt ist ein Default-Objekt
                        return array($this->option[$option]);
                    } else {
                        // Dieses Element ist aktiv aber es wurde kein Objekt definiert.
                        return true;
                    }

                } else {
                    if (is_object($return) && !$return->isdefault()) {
                        // Objekt von Kind-Element zurueckgeben
                        return $return;
                    } elseif (isset($this->option[$option])) {
                        // $return ist kein Objekt (z.B. TRUE) ODER
                        // das Objekt ist ein Default-Objekt
                        return $this->option[$option];
                    } else {
                        // Dieses Element ist aktiv aber es wurde kein Objekt definiert.
                        return true;
                    }
                }
            }

        }
        return false;
    }

//== ArrayAccess methodes ========================================

    /**
     * @param string $visible
     *
     * @return bool
     */
    public
    function isvisible(
        $visible
    ) {
        return $this->option[__NAMESPACE__.'\\Option\\Visible']->isvisible($visible);
    }

    /**
     * these methodes are needed to treat an object like an array
     *
     * @param int $offset
     *
     * @return nav
     */
    public
    function offsetGet(
        $offset
    ) {
        return $this->_subnavs[$offset];
    }

    /**
     * @param int $offset
     * @param nav $value
     */
    public
    function offsetSet(
        $offset,
        $value
    ) {
        if (is_null($offset)) {
            $this->_subnavs[] = $value;
        } else {
            $this->_subnavs[$offset] = $value;
        }
    }

    /**
     * @param int $offset
     *
     * @return bool
     */
    public
    function offsetExists(
        $offset
    ) {
        return isset($this->_subnavs[$offset]);
    }

//== Iterator methodes ===========================================

    /**
     * @param int $offset
     */
    public
    function offsetUnset(
        $offset
    ) {
        unset($this->_subnavs[$offset]);
    }

    /**
     * methodes needed to iterate through the sub-nav-object with a foreach
     */
    public
    function rewind()
    {
        $this->_iterator = 0;
    }

    /**
     * @return nav
     */
    public
    function current()
    {
        return $this->_subnavs[$this->_iterator];
    }

    /**
     * @return int
     */
    public
    function key()
    {
        return $this->_iterator;
    }

    public
    function next()
    {
        $this->_iterator++;
    }

    /**
     * @return bool
     */
    public
    function valid()
    {
        return isset($this->_subnavs[$this->_iterator]);
    }
}


