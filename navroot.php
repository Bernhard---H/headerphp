<?php
namespace htlwy\Header;

/**
 * Class navroot stellt die Basis des gesamten Objektbaums dar.
 *
 * Die Klasse ist dafuer gedacht, Funktion bereit zu stellen,
 * um den Code in header.php zu vereinfachen
 */
class Navroot
{
    /**
     * Home-Pfad des Projekts relativ zum Document-Root des Webservers
     *
     * If the path doesn't start with a slash, the functions will first look
     * in the directory navroot::$home to find the file of the nav element
     * navroot::$home is always used as extend of $_SERVER['DOCUMENT_ROOT']
     *
     * <code>
     *      // www.mydomain.com
     *      public static $home = '/';
     *      // www.mydomain.com/myhomepage
     *      public static $home = '/myhomepage';
     * </code>
     *
     * @var string
     */
    public static $home = HP_HEADER;

    /**
     * Sets the current chosen language. At websites with only one language
     * this can be ignored.
     *
     * <code>
     *      navroot::$lang = $_GET['lang'];
     * </code>
     *
     * @var string
     */
    public static $lang = 'default';

    /**
     * // Language that is used, if the one in navroot::$lang is not set
     *
     * @var string
     */
    public static $default_lang = 'default';

    /**
     * allows multiple nav elements to be marked as active on the same level
     *
     * @var bool
     */
    public static $multi_active = true;

    /**
     * class for active navigation <a> elements
     *
     * @var string
     */
    public static $nav_activ_class = 'active';

    /**
     * class for theactive navigation <a> element
     *
     * @var string
     */
    public static $nav_theactiv_class = 'active';

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

    /**
     * full sitemap, built with the nav class
     *
     * @var Nav[]
     */
    protected $navtree = array();

    /**
     * default values for option functions
     *
     * @var array
     */
    protected $current = array();

    /**
     * @param Nav[] $nav
     */
    public function __construct($nav = array(null))
    {
        $args = func_num_args();
        for ($i = 0; $i < $args; $i++) {
            $arg = func_get_arg($i);

            if ($arg instanceof nav) {
                $this->navtree[count($this->navtree)] = $arg;
            }
        }
    }

    /**
     * add a nav element to the navigation tree
     *
     * @param nav[] $nav
     */
    public function add($nav = array(null))
    {
        $args = func_num_args();
        for ($i = 0; $i < $args; $i++) {
            $arg = func_get_arg($i);

            if ($arg instanceof nav) {
                $this->navtree[count($this->navtree)] = $arg;
            }
        }
    }

    /**
     * Ermöglicht das Überschreiben der Eigenschaften, die aufgrund des
     * `active` Wertes ausgewählt werden. Die Funktion `getactiveoption()`
     * nimmt automatisch darauf rücksicht.
     *
     * @param option $option Objket der Eigenschaft, die überschrieben
     *                       werden soll.
     */
    public function setCurrent(option $option)
    {
        $this->current[get_class($option)] = $option;
    }

    /**
     * calls the getactiveoption function for the title and lines up the
     * titles with a delimiter
     *
     * @return string
     */
    public function gettitle()
    {
        $ret    = '';
        $first  = true;
        $titles = $this->getactiveoption('Title', true);
        if (is_array($titles)) {
            foreach ($titles as $title) {
                if ($first) {
                    $first = false;
                } else {
                    $ret .= ' | ';
                }
                $ret .= $title->get();
            }
        }
        elseif(is_object($titles) && $titles instanceof option)
        {
            if(!$titles->isdefault())
            {
                $ret = $titles->get();
            }
        }
        return $ret;
    }

    /**
     * Searches for the activ option object
     *
     * @param      $option
     * @param bool $all if $all isset, the function returns all non default object
     *                  or a default one if all active are default and no default value
     *                  is set.
     *
     * @throws \Exception
     * @return option[]|bool
     */
    public function getactiveoption($option, $all = false)
    {
        $option = __NAMESPACE__.'\\Option\\'.$option;

        if (is_subclass_of($option, __NAMESPACE__.'\\Option')) {

            if (isset($this->current[$option])) {
                // wurde von Benutzer ueberschrieben
                return $this->current[$option];
            }

            $ret = false;
            foreach ($this->navtree as $value) {
                $ret = $value->getactiveoption($option, $all);
                if ($ret !== false) {
                    break;
                }
            }
            return $ret;
        } else {
            throw new \Exception('Unbekannte Option: "'.$option.'"');
        }
    }

    /**
     * Returns a simple navigation built with an HTML list
     *
     * @param string $visible  will be the parameter of the isvisible(); method
     *                         of the visible class
     * @param int    $from     navigation will start with a subnav; 0 is this one
     * @param int    $to       defines where to stop adding sub elements
     * @param bool   $noactive function will ignore the status of activ e.g.: Sitemap
     * @param null   $obj      runs the function on the given object, $noactive == true!
     *
     * @return string
     */
    public function getnavigation(
            $visible = 'all',
            $from = 0,
            $to = -1,
            $noactive = false,
            $obj = null
    ){
        $ret = '';
        if ($to != 0) {
            $first = true;
            if ($obj instanceof nav) {
                $subret = $obj->navigation($visible, $from, $to, true);
                if ($subret != '') {
                    if ($first) {
                        $ret .= "\n<ul>\n";
                        $first = false;
                    }
                    $ret .= $subret;
                }
            } else {
                foreach ($this->navtree as $subnav) {
                    $subret = $subnav->navigation($visible, $from, $to, $noactive);
                    if ($subret != '') {
                        if ($first) {
                            $ret .= "\n<ul>\n";
                            $first = false;
                        }
                        $ret .= $subret;
                        if ($noactive == false) {
                            break;
                        }
                    }
                }
            }
            if (!$first) {
                $ret .= "</ul>\n";
            }
        }
        return $ret;
    }

    /**
     * Returns an array of <a></a> tags with the links of the navigation
     *
     * @param string $visible    will be the parameter of the isvisible(); method
     *                           of the visible class
     * @param int    $from       navigation will start with a subnav; 0 is this one
     * @param int    $to         defines where to stop adding sub elements
     * @param bool   $activeonly function will return only links form the active ones
     * @param null   $obj        runs the function on the given object
     *
     * @return array
     */
    public function getnavlinks(
            $visible = 'all',
            $from = 0,
            $to = -1,
            $activeonly = false,
            $obj = null
    ){
        $ret = array();
        $i   = 0;

        if ($to != 0) {
            if ($obj instanceof nav) {
                $subret = $obj->navlinks($visible, $from, $to, $activeonly);
                if ($subret[0] != '') {
                    $ret[$i] = $subret[0];
                    $i++;
                }
                if (isset($subret[1])) {
                    $ret[$i] = $subret[1];
                }
            } else {
                foreach ($this->navtree as $subnav) {
                    $subret = $subnav->navlinks($visible, $from, $to, $activeonly);
                    if ($subret[0] != '') {
                        $ret[$i] = $subret[0];
                        $i++;
                    }
                    if (isset($subret[1])) {
                        $ret[$i] = $subret[1];
                        $i++;
                    }
                }
            }
        }
        return $ret;
    }

    /**
     * Returns a http://www.sitemaps.org/ XML-Sitemap
     *
     * @param string $visible will be the parameter of the isvisible(); method of the visible class
     *
     * @return string
     */
    public function xmlsitemap($visible = 'all')
    {
        $ret = '<?xml version="1.0" encoding="UTF-8"?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
';
        foreach ($this->navtree as $subnav) {
            $ret .= $subnav->xmlsitemap($visible);
        }
        $ret .= '</urlset>';

        return $ret;
    }

}

