<?php
/**
 * Bu Dosya AnonymFramework'e ait bir dosyadır.
 *
 * @author vahitserifsaglam <vahit.serif119@gmail.com>
 * @see http://gemframework.com
 *
 */


namespace Sagi\Database\Event;
use Exception;

/**
 * Class EventNotFoundException
 * @package Sagi\Database\Event
 */
class EventNotFoundException extends Exception
{


    /**
     * İstisnayı oluşturur
     *
     * @param string $message
     */
    public function __construct($message = '')
    {

        $this->message = $message;
    }

}
