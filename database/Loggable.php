<?php
/**
 *  SAGI DATABASE ORM FILE
 *
 */

namespace Sagi\Database;


class Loggable implements LoggerAwareInterface
{

    /**
     * logger instance
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Loggable constructor.
     */
    public function __construct()
    {
        $this->setLogger(new Logger());

        set_error_handler([$this, 'errorHandler']);
        set_exception_handler([$this, 'exceptionHandler']);

        if(!file_exists('logs') && !dir('logs')){
            mkdir('logs', 0777);
        }


    }

    /**
     * sets a logger interface on the object
     *
     * @param LoggerInterface $logger
     * @return mixed
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }


    /**
     * @param $code
     * @param $message
     * @param $file
     * @param $line
     * @throws ErrorException
     */
    public function errorHandler($code, $message, $file, $line)
    {
        throw new ErrorException($message, $code, $line, $file);
    }

    /**
     * @param \Exception $exception
     */
    public function exceptionHandler(\Exception $exception)
    {
        $message = $exception->getMessage();
        $code = $exception->getCode();
        $line = $exception->getLine();
        $file = $exception->getFile();


        $context = compact('code', 'line', 'file');

        switch ($code) {
            case E_USER_ERROR:
                $this->logger->critical($message, $context);
                break;
            case E_COMPILE_ERROR:
                $this->logger->emergency($message, $context);
            case E_CORE_ERROR:
                $this->logger->error($message, $context);
                break;
            case E_USER_NOTICE:
            case E_USER_WARNING:
                $this->logger->warning($message, $context);
                break;
            case 0:
                $this->logger->info($message, $context);
                break;
            default:
                $this->logger->notice($message, $context);
                break;
        }
    }

}