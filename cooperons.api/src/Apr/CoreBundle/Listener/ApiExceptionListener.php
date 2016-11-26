<?php
namespace Apr\CoreBundle\Listener;

use Apr\CoreBundle\ApiException\ApiException;
use Apr\CoreBundle\ApiResponse\ApiResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;


class ApiExceptionListener
{
    protected $kernel;

    public function __construct($kernel)
    {
        $this->kernel = $kernel;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if ($exception instanceof ApiException) {
            $code = $exception->getErrorCode();
            $message = $exception->getMessage();
            $status = $exception->getStatus();
            $data = $exception->getData();
        } else {
            if ($exception instanceof HttpExceptionInterface && method_exists($exception, "getStatusCode")) {
                $code = $exception->getStatusCode();
            } else {
                $code = 500;
            }
            $status = 'error';
            $message = $this->kernel->getEnvironment() == 'dev' ? $exception->getMessage() : 'An internal error has occurred';
            $data = null;
        }

        $response = new ApiResponse($data, $code, $message, $status);
        $event->setResponse($response);
    }
}

?>
