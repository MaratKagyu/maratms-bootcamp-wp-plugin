<?php
/**
 * Created by PhpStorm.
 * User: MaratMS
 * Date: 10/24/2018
 * Time: 12:09 AM
 */

namespace MaratMSBootcampPlugin\Tools;


class BootcampBackendResponse
{
    /**
     * @var int
     */
    private $httpCode = 0;

    /**
     * @var string
     */
    private $response = "";

    /**
     * BootcampBackendResponse constructor.
     * @param int $httpCode
     * @param string $response
     */
    public function __construct($httpCode, $response)
    {
        $this->httpCode = (int)$httpCode;
        $this->response = $response;
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->getHttpCode() < 400;
    }

    /**
     * @return bool
     */
    public function isForbidden()
    {
        return $this->getHttpCode() === 403;
    }

    /**
     * @return bool
     */
    public function isNotFound()
    {
        return $this->getHttpCode() === 404;
    }

    /**
     * @return bool
     */
    public function isBadRequest()
    {
        return $this->getHttpCode() === 400;
    }

    /**
     * @return int
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }

    /**
     * @return string
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return array
     */
    public function getResponseJson()
    {
        return json_decode($this->response, true);
    }
}