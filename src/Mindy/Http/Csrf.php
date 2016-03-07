<?php

namespace Mindy\Http;

use Mindy\Exception\HttpException;
use Mindy\Base\Mindy;
use Mindy\Helper\Traits\Accessors;
use Mindy\Helper\Traits\Configurator;
use Mindy\Security\SecurityManager;

/**
 * Class Csrf
 * @package Mindy\Http
 */
class Csrf
{
    use Accessors, Configurator;

    /**
     * @var boolean whether to enable CSRF (Cross-Site Request Forgery) validation. Defaults to false.
     * By setting this property to true, forms submitted to an Yii Web application must be originated
     * from the same application. If not, a 400 HTTP exception will be raised.
     * Note, this feature requires that the user client accepts cookie.
     * You also need to use {@link CHtml::form} or {@link CHtml::statefulForm} to generate
     * the needed HTML forms in your pages.
     * @see http://seclab.stanford.edu/websec/csrf/csrf.pdf
     */
    public $enableCsrfValidation = true;
    /**
     * @var string the name of the token used to prevent CSRF. Defaults to 'YII_CSRF_TOKEN'.
     * This property is effectively only when {@link enableCsrfValidation} is true.
     */
    public $csrfTokenName = 'X-CSRFToken';
    /**
     * @var array the property values (in name-value pairs) used to initialize the CSRF cookie.
     * Any property of {@link HttpCookie} may be initialized.
     * This property is effective only when {@link enableCsrfValidation} is true.
     */
    public $csrfCookie;
    /**
     * @var string csrf token value
     */
    private $_csrfToken;

    private $securityManager;

    private $cookies;

    private $http;

    public function __construct(SecurityManager $securityManager, CookieCollection $cookies, Http $http, array $config = [])
    {
        $this->securityManager = $securityManager;
        $this->cookies = $cookies;
        $this->http = $http;
        $this->configure($config);
    }

    public function getName()
    {
        return $this->csrfTokenName;
    }

    public function getValue()
    {
        return $this->getCsrfToken();
    }

    /**
     * Returns the random token used to perform CSRF validation.
     * The token will be read from cookie first. If not found, a new token
     * will be generated.
     * @return string the random token for CSRF validation.
     * @see enableCsrfValidation
     */
    public function getCsrfToken()
    {
        if ($this->_csrfToken === null) {
            $cookie = $this->cookies->get($this->csrfTokenName);
            if (!$cookie || ($this->_csrfToken = $cookie->value) == null) {
                $cookie = $this->createCsrfCookie();
                $this->_csrfToken = $cookie->value;
                $this->cookies->add($cookie->name, $cookie);
            }
        }

        return $this->_csrfToken;
    }

    /**
     * Creates a cookie with a randomly generated CSRF token.
     * Initial values specified in {@link csrfCookie} will be applied
     * to the generated cookie.
     * @return Cookie the generated cookie
     * @see enableCsrfValidation
     */
    protected function createCsrfCookie()
    {
        $cookie = new Cookie($this->csrfTokenName, sha1(uniqid(mt_rand(), true)));
        if (is_array($this->csrfCookie)) {
            foreach ($this->csrfCookie as $name => $value) {
                $cookie->$name = $value;
            }
        }
        return $cookie;
    }

    /**
     * Performs the CSRF validation.
     * This is the event handler responding to {@link CApplication::onBeginRequest}.
     * The default implementation will compare the CSRF token obtained
     * from a cookie and from a POST field. If they are different, a CSRF attack is detected.
     * @throws HttpException
     */
    public function getIsValid()
    {
        if ($this->http->getIsPostRequest() || $this->http->getIsPutRequest() || $this->http->getIsDeleteRequest()) {
            $userToken = null;
            $method = $this->http->getRequestType();
            switch ($method) {
                case 'POST':
                    $userToken = $this->http->getPost($this->csrfTokenName);
                    break;
                case 'PUT':
                    $userToken = $this->http->getPut($this->csrfTokenName);
                    break;
                case 'DELETE':
                    $userToken = $this->http->getDelete($this->csrfTokenName);
                    break;
            }

            if (empty($userToken)) {
                $userToken = $this->http->getHeaderValue($this->csrfTokenName);
            }

            if (!empty($userToken) && $this->cookies->has($this->csrfTokenName)) {
                $cookieToken = $this->cookies->get($this->csrfTokenName)->value;
                // https://github.com/studio107/Mindy_Base/issues/1
                $unserializedHashData = @unserialize($this->securityManager->validateData($userToken));
                $valid = $cookieToken === $userToken || $cookieToken === $unserializedHashData;
            } else {
                $valid = false;
            }

            return $valid;
        } else {
            return true;
        }
    }

    /**
     * Performs the CSRF validation.
     * This is the event handler responding to {@link CApplication::onBeginRequest}.
     * The default implementation will compare the CSRF token obtained
     * from a cookie and from a POST field. If they are different, a CSRF attack is detected.
     * @throws HttpException
     */
    public function validate()
    {
        if (!$this->getIsValid()) {
            throw new HttpException(400, Mindy::t('base', 'The CSRF token could not be verified.'));
        }
    }
}
