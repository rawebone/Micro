<?php
namespace Micro\Testing;

use Symfony\Component\BrowserKit\Client as BaseClient;
use Symfony\Component\BrowserKit\Request as BaseReq;
use Symfony\Component\BrowserKit\Response as BaseResp;
use Symfony\Component\BrowserKit\History;
use Symfony\Component\BrowserKit\CookieJar;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Micro\ApplicationInterface;
use Micro\Request;

class Client extends BaseClient
{
    protected $application;
    
    public function __construct(ApplicationInterface $app, array $server = array(), History $history = null, CookieJar $cookieJar = null)
    {
        $this->application = $app;
        $this->followRedirects = false;
        $this->insulated = false;
        
        parent::__construct($server, $history, $cookieJar);
    }

    /**
     * @return \Micro\ApplicationInterface
     */
    public function getApp()
    {
        return $this->application;
    }
    
    protected function doRequest($request)
    {
        $this->application->run($request);
        return $this->application->lastResponse();
    }
    
    /**
     * @param \Symfony\Component\BrowserKit\Request $request
     * @return \Micro\Request
     */
    protected function filterRequest(BaseReq $request)
    {
        $request = Request::create(
                $request->getUri(), 
                $request->getMethod(), 
                $request->getParameters(), 
                $request->getCookies(), 
                $request->getFiles(), 
                $request->getServer(), 
                $request->getContent()
        );
        
        $request->files->replace($this->filterFiles($request->files->all()));
        return $request;
    }
    
    
    /**
     * This method has been copied from the \Symfony\Component\HttpKernel\Client.
     * 
     * ==========================
     * 
     * Filters an array of files.
     *
     * This method created test instances of UploadedFile so that the move()
     * method can be called on those instances.
     *
     * If the size of a file is greater than the allowed size (from php.ini) then
     * an invalid UploadedFile is returned with an error set to UPLOAD_ERR_INI_SIZE.
     *
     * @see Symfony\Component\HttpFoundation\File\UploadedFile
     * @param array $files An array of files
     * @return array An array with all uploaded files marked as already moved
     */
    protected function filterFiles(array $files)
    {
        $filtered = array();
        foreach ($files as $key => $value) {
            if (is_array($value)) {
                $filtered[$key] = $this->filterFiles($value);
            } elseif ($value instanceof UploadedFile) {
                if ($value->isValid() && $value->getSize() > UploadedFile::getMaxFilesize()) {
                    $filtered[$key] = new UploadedFile(
                        '',
                        $value->getClientOriginalName(),
                        $value->getClientMimeType(),
                        0,
                        UPLOAD_ERR_INI_SIZE,
                        true
                    );
                } else {
                    $filtered[$key] = new UploadedFile(
                        $value->getPathname(),
                        $value->getClientOriginalName(),
                        $value->getClientMimeType(),
                        $value->getClientSize(),
                        $value->getError(),
                        true
                    );
                }
            } else {
                $filtered[$key] = $value;
            }
        }

        return $filtered;
    }

    protected function filterResponse($response)
    {
        if ($response === false) { // Failed request
            return new BaseResp("", 404);
        }
        
        $headers = $response->headers->all();
        if ($response->headers->getCookies()) {
            $cookies = array();
            foreach ($response->headers->getCookies() as $cookie) {
                $cookies[] = new Cookie($cookie->getName(), $cookie->getValue(), $cookie->getExpiresTime(), $cookie->getPath(), $cookie->getDomain(), $cookie->isSecure(), $cookie->isHttpOnly());
            }
            $headers["Set-Cookie"] = $cookies;
        }

        // this is needed to support StreamedResponse
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        return new BaseResp($content, $response->getStatusCode(), $headers);
    }

    /**
     * @return \Micro\Request
     */
    public function getRequest()
    {
        return parent::getRequest();
    }

    /**
     * Insulation is not supported by the Micro Client. An exception will be
     * called if attempted.
     * 
     * @param boolean $insulated
     * @throws \ErrorException
     */
    public function insulate($insulated = true)
    {
        throw new \ErrorException("Insulation is not supported by Micro");
    }
}
