<?php
namespace htlwy\headerphp\Option;

use htlwy\headerphp\Option;

/**
 * Class CSP
 * Stellt den CSP-header zusammen
 */
class CSP extends Option
{
    /**
     * Standardeinstellung fuer die einzelnen Werte.
     *
     * @var array
     */
    protected static $default = array("default-src" => "'none'", "script-src" => "'self'",
            "style-src" => "'self' 'unsafe-inline'", "img-src" => "'self'", "connect-src" => "'self'",
            "form-action" => "'self'", "reflected-xss" => "block");

    /**
     * Array von Arrays mit ergaenzungen zu $default
     *
     * @var array
     */
    protected $add = array();
    /**
     * Gibt an, ob der CSP-Header angewendet werden soll; wird aktiviert durch `true` oder ergaenzen von $default
     *
     * @var bool
     */
    protected $activated = true;

    protected $csp = '';

    /**
     * @param bool|array $csp
     */
    public function __construct($csp = null)
    {
        self::$default["report-uri"] = HP_HEADER."/xss_report.php";
        if(isset($csp))
        {
            $this->set($csp);
        }
    }

    /**
     * Aktiviert durch `true` den Default-Header und ergaenzt in gleichzeitig bei uebergeben eines arrays
     *
     * @param bool|array $csp
     *
     * @throws InvalidArgumentException
     */
    public function set($csp)
    {
        if(is_bool($csp))
        {
            $this->activated = $csp;
        }
        elseif(is_array($csp))
        {
            $this->add[] = $csp;
        }
        else
        {
            throw new InvalidArgumentException('The CSP-value has to be boolean or array!');
        }
        $this->_isdefault = false;
    }

    /**
     * Berechnet den CSP-Header-String aus den Default-Werten und den Ergaenzungen
     *
     * @return string
     */
    public function get()
    {
        if(!$this->activated)
        {
            return '';
        }

        $ret = self::$default;

        foreach($this->add as $add)
        {
            foreach($add as $policy => $uri)
            {
                if(isset($ret[$policy]))
                {
                    if($ret[$policy] == "'none'")
                    {
                        $ret[$policy] = '';
                    }
                    // reflected-xss: Spezial-Policy, erwartet nur ein schluesselwort -> ueberschreiben
                    if($policy == "reflected-xss")
                    {
                        $ret[$policy] = $uri;
                    }
                    else
                    {
                        $ret[$policy] .= " ".trim($uri);
                    }
                }
                else
                {
                    $ret[$policy] = trim($uri);
                }
            }
        }

        return $this->imploder($ret);
    }

    /**
     * Generiert aus dem CSP-Array den Header-String
     *
     * @param $array
     *
     * @return string
     */
    protected function imploder($array)
    {
        $ret = '';
        foreach($array as $key => $value)
        {
            $ret .= $key.' '.$value.'; ';
        }
        return $ret;
    }
}