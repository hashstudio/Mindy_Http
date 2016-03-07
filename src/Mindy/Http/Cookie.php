<?php

namespace Mindy\Http;

use Mindy\Helper\Traits\Accessors;
use Mindy\Helper\Traits\Configurator;

/**
 * A Cookie instance stores a single cookie, including the cookie name, value, domain, path, expire, and secure.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @package Mindy\Http
 * @since 1.0
 */
class Cookie
{
    use Configurator, Accessors;

    /**
     * @var string name of the cookie
     */
    public $name;
    /**
     * @var string value of the cookie
     */
    public $value = '';
    /**
     * @var string domain of the cookie
     */
    public $domain = '';
    /**
     * @var integer the timestamp at which the cookie expires. This is the server timestamp. Defaults to 0, meaning "until the browser is closed".
     */
    public $expire = 0;
    /**
     * @var string the path on the server in which the cookie will be available on. The default is '/'.
     */
    public $path = '/';
    /**
     * @var boolean whether cookie should be sent via secure connection
     */
    public $secure = false;
    /**
     * @var boolean whether the cookie should be accessible only through the HTTP protocol.
     * By setting this property to true, the cookie will not be accessible by scripting languages,
     * such as JavaScript, which can effectly help to reduce identity theft through XSS attacks.
     * Note, this property is only effective for PHP 5.2.0 or above.
     */
    public $httpOnly = false;

    /**
     * Constructor.
     * @param string $name name of this cookie
     * @param string $value value of this cookie
     * @param array $options the configuration array consisting of name-value pairs
     * that are used to configure this cookie
     */
    public function __construct($name, $value, $options = [])
    {
        $this->name = $name;
        $this->value = $value;

        unset($options['name'], $options['value']);

        $data = session_get_cookie_params();
        $data['httpOnly'] = $data['httponly'];
        $data['expire'] = $data['lifetime'];
        unset($data['lifetime'], $data['httponly']);
        $this->configure(array_merge($data, $options));
    }

    /**
     * Magic method to use the cookie object as a string without having to call value property first.
     * <code>
     * $value = (string)$cookies['name'];
     * </code>
     * Note, that you still have to check if the cookie exists.
     * @return string The value of the cookie. If the value property is null an empty string will be returned.
     * @since 1.1.11
     */
    public function __toString()
    {
        return (string)$this->value;
    }
}
