<?php
namespace Micro;

use Symfony\Component\HttpFoundation as HF;

class Responder
{
    public function redirect($to, $status = 302, array $headers = array())
    {
        return new HF\RedirectResponse($to, $status, $headers);
    }
    
    public function json($data, $status = 200, array $headers = array())
    {
        return new HF\JsonResponse($data, $status, $headers);
    }
    
    public function standard($content = '', $status = 200, array $headers = array())
    {
        return new HF\Response($content, $status, $headers);
    }
    
    public function file($file, $status = 200, $headers = array(), $public = true, $contentDisposition = null, $autoEtag = false, $autoLastModified = true)
    {
        return new HF\BinaryFileResponse($file, $status, $headers, $public, $contentDisposition, $autoEtag, $autoLastModified);
    }
    
    public function streamed($callback = null, $status = 200, $headers = array())
    {
        return new HF\StreamedResponse($callback, $status, $headers);
    }
}
