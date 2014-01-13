<?php
namespace Micro\Util;

/**
 * UrlTools provides simple handling for creating URL matching regular 
 * expressions and capturing the parameters of those URL's.
 */
class UrlTools
{
    /**
     * Returns a compiled url regex.
     * 
     * @param string $url The parameterised url string
     * @param array $conditions An array containing the regular expression conditions to be merged
     * @return string
     */
    public function compile($url, array $conditions)
    {
        $replacer = function (array $match) use ($conditions) {
            $name = $match[2];
            $expr = (isset($conditions[$name]) ? $conditions[$name] : "[^/]+");
            
            return "(?<$name>$expr)";
        };
        
        $regex = preg_replace_callback("#(\{([A-Za-z0-9]+)\})+#", $replacer, $url);
        return "#^$regex$#";
    }
    
    /**
     * Returns whether the given URL regex matches the given query string.
     * 
     * @param string $regex
     * @param string $queryString
     * @return boolean
     */
    public function match($regex, $queryString)
    {
        $match = preg_match($regex, rawurldecode($queryString));
        return $match === 1;
    }
    
    /**
     * Returns an array of the parameters from a query string based on
     * the compiled regex.
     * 
     * @param string $regex
     * @param string $queryString
     * @return array
     */
    public function parameters($regex, $queryString)
    {
        preg_match($regex, rawurldecode($queryString), $matches);
        
        $params = array();
        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                $params[$key] = $value;
            }
        }
        
        return $params;
    }
}
