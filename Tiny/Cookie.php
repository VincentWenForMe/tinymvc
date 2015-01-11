<?php
/**
 * Created by hisune.com
 * User: hi@hisune.com
 * Date: 14-7-11
 * Time: 下午4:06
 *
 * from micromvc
 */
namespace Tiny;

class Cookie
{

    public static $settings = array();

    /**
     * Decrypt and fetch cookie data
     *
     * @param string $name of cookie
     * @param array $config settings
     * @return mixed
     */
    public static function get($name, $config = NULL)
    {
        // Use default config settings if needed
        $config = $config ?: static::$settings;

        if(isset($_COOKIE[$name]))
        {
            // Decrypt cookie using cookie key
            if($v = json_decode(Cipher::decrypt(base64_decode($_COOKIE[$name]), Config::config()->key)))
            {
                // Has the cookie expired?
                if($v[0] < $config['timeout'])
                {
                    return is_scalar($v[1])?$v[1]:(array)$v[1];
                }
            }
        }

        return FALSE;
    }


    /**
     * Called before any output is sent to create an encrypted cookie with the given value.
     *
     * @param $name
     * @param mixed $value to save
     * @param array $config settings
     * return boolean
     * @internal param string $key cookie name
     */
    public static function set($name, $value, $config = NULL)
    {
        // Use default config settings if needed
        extract($config ?: static::$settings);

        // If the cookie is being removed we want it left blank
        $value = $value ? base64_encode(Cipher::encrypt(json_encode(array(time(), $value)), Config::config()->key)) : '';

        // Save cookie to user agent
        setcookie($name, $value, $expires, $path, $domain, $secure, $httponly);
    }

}

// END
