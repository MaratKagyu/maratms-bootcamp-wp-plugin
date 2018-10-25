<?php
/**
 * Created by PhpStorm.
 * User: MaratMS
 * Date: 10/23/2018
 * Time: 9:37 PM
 */

namespace MaratMSBootcampPlugin\Tools;

use MaratMSBootcampPlugin\Entity\Quote;
use MaratMSBootcampPlugin\Exception\BootcampException;

class BootcampBackend
{
    /**
     * @var string
     */
    private $backendUrl = '';

    /**
     * @var string
     */
    private $backendToken = '';

    /**
     * BootcampBackend constructor.
     * @param string $backendUrl
     * @param string $backendToken
     */
    public function __construct($backendUrl, $backendToken = '')
    {
        $this->backendUrl = $backendUrl;
        $this->backendToken = $backendToken;

    }

    /**
     * @param string $method
     * @param string $url
     * @param array $queryParams
     * @param array $requestParams
     * @return BootcampBackendResponse
     */
    private function makeCustomRequest($method, $url, $queryParams = [], $requestParams = [])
    {
        $method = mb_strtoupper($method);

        $ch = curl_init();

        $url =
            $this->backendUrl . $url
            . (count($queryParams) ? "?" . http_build_query($queryParams) : "")
        ;
        curl_setopt($ch, CURLOPT_URL, $url);

        switch ($method) {
            case 'GET':
                curl_setopt($ch, CURLOPT_POST, 0);
                break;

            case 'POST':
                curl_setopt($ch, CURLOPT_POST, 1);
                if (count($requestParams)) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($requestParams));
                }
                break;

            default:
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                if (count($requestParams)) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($requestParams));
                }
                break;
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_TIMEOUT, 10); // max timeout 10 sec
        $rawResult = curl_exec($ch);
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close ($ch);

        return new BootcampBackendResponse($httpStatus, $rawResult);
    }

    /**
     * @param $url
     * @param $queryParams
     * @return BootcampBackendResponse
     */
    private function makeGetRequest($url, $queryParams = [])
    {
        return $this->makeCustomRequest('GET', $url, $queryParams);
    }

    /**
     * @param $url
     * @param $queryParams
     * @param $requestParams
     * @return BootcampBackendResponse
     */
    private function makePostRequest($url, $queryParams = [], $requestParams = [])
    {
        return $this->makeCustomRequest('POST', $url, $queryParams, $requestParams);
    }

    /**
     * @param $url
     * @param $queryParams
     * @return BootcampBackendResponse
     */
    private function makeDeleteRequest($url, $queryParams = [])
    {
        return $this->makeCustomRequest('DELETE', $url, $queryParams);
    }

    /**
     * @return Quote[]
     */
    public function loadQuoteList()
    {
        $quoteListData = $this->makeGetRequest('/quotes', ["token" => $this->backendToken])->getResponseArray();
        return array_map(
            function ($quoteData) {
                return Quote::constructFromArray($quoteData);
            },
            $quoteListData
        );
    }

    /**
     * @return void
     * @throws BootcampException
     */
    public function register()
    {
        $quoteResponse = $this->makePostRequest(
            '/register',
            [],
            ["token" => $this->backendToken]
        );

        if (! $quoteResponse->isSuccessful()) {
            throw new BootcampException("Couldn't register the app!");
        }
    }

    /**
     * @param int $quoteId
     * @return Quote|null
     */
    public function loadQuote($quoteId)
    {
        $quoteResponse = $this->makeGetRequest(
            '/quotes/' . (int)$quoteId,
            ["token" => $this->backendToken]
        );

        return $quoteResponse->isSuccessful()
            ?  Quote::constructFromArray($quoteResponse->getResponseArray())
            : null
        ;
    }

    /**
     * @param int $authorId
     * @return Quote[]
     */
    public function loadAuthorQuotes($authorId)
    {
        $quoteResponse = $this->makeGetRequest(
            '/quotes/by_author/' . (int)$authorId,
            ["token" => $this->backendToken]
        );

        return $quoteResponse->isSuccessful()
            ?  array_map(
                function ($quoteData) {
                    return Quote::constructFromArray($quoteData);
                },
                $quoteResponse->getResponseArray()
            )
            : []
        ;
    }

    /**
     * @return Quote|null
     */
    public function loadRandomQuote()
    {
        $quoteResponse = $this->makeGetRequest(
            '/quotes/random',
            ["token" => $this->backendToken]
        );

        return $quoteResponse->isSuccessful()
            ?  Quote::constructFromArray($quoteResponse->getResponseArray())
            : null
        ;
    }

    /**
     * @param int $quoteId
     * @param string $authorName
     * @param string $quoteText
     * @return Quote
     */
    public function saveQuote($quoteId, $authorName, $quoteText)
    {
        $quoteData = $this->makePostRequest(
            '/quotes/' . (int)$quoteId,
            [],
            [
                "token" => $this->backendToken,
                "authorName" => $authorName,
                "text" => $quoteText,
            ]
        )->getResponseArray();

        return Quote::constructFromArray($quoteData);
    }

    /**
     * @param int $quoteId
     */
    public function deleteQuote($quoteId)
    {
        $this->makeDeleteRequest(
            '/quotes/' . (int)$quoteId,
            ["token" => $this->backendToken]
        );
    }
}