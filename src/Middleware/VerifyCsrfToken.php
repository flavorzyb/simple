<?php
namespace Simple\Middleware;

use Closure;
use Simple\Http\Request;
use Simple\Support\CSRFToken;

class VerifyCsrfToken
{
    /**
     * @var CSRFToken
     */
    protected $token = null;

    public function __construct(CSRFToken $token)
    {
        $this->token = $token;
    }

    /**
     * Handle an incoming request.
     * @param Closure $next
     * @return bool
     */
    public function handle(Closure $next)
    {
        if ($this->isReading() || $this->tokensMatch()) {
            return $next();
        }

        return false;
    }

    /**
     * get token string
     * @return string
     */
    protected function getTokenString()
    {
        $result = Request::post('_token', '');
        if ('' == $result) {
            $result = Request::get('_token', '');
        }

        if ('' == $result) {
            $result = isset($_SERVER['X-CSRF-TOKEN']) ? $_SERVER['X-CSRF-TOKEN'] : '';
        }

        return $result;
    }

    /**
     * Determine if the HTTP request uses a ‘read’ verb.
     * @return bool
     */
    protected function isReading()
    {
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
        return in_array($method, ['HEAD', 'GET', 'OPTIONS']);
    }

    /**
     * Determine if the session and input CSRF tokens match.
     *
     * @return bool
     */
    protected function tokensMatch()
    {
        $tokenString = $this->getTokenString();
        if (!is_string($tokenString)) {
            return false;
        }

        return $this->token->matchCSRFString($tokenString);
    }
}
