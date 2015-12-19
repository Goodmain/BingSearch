<?php

namespace Goodmain\BingSearch;

use Goodmain\BingSearch\Objects\Error;
use Goodmain\BingSearch\Objects\Response;
use Goodmain\BingSearch\Objects\Result;

class BingSearch
{
    /**
     * AccountKey from page https://datamarket.azure.com/account/keys
     * @var string
     */
    protected $accountKey;

    /**
     * Type of executed search
     * @var string
     */
    protected $searchType;

    /**
     * Options string
     * @var string
     */
    protected $options = "";

    /**
     * Predefined values of Options
     * @var array
     */
    protected $optionsList = ['DisableLocationDetection', 'EnableHighlighting'];

    /**
     * WebSearchOptions string
     * @var string
     */
    protected $webOptions = "";

    /**
     * Predefined values of WebSearchOptions
     * @var array
     */
    protected $webOptionsList = ['DisableQueryAlterations', 'DisableHostCollapsing'];

    const TYPE_WEB = 'Web';
    const TYPE_IMAGE = 'Image';
    const TYPE_VIDEO = 'Video';
    const TYPE_NEWS = 'News';
    const TYPE_SPELL = 'Spell';
    const TYPE_RELATED = 'Related';

    const FORMAT_JSON = 'JSON';
    const FORMAT_XML = 'Atom';
    const FORMAT_OBJECT = 'Object';

    /**
     * @var string
     */
    protected $uri = 'https://api.datamarket.azure.com/Bing/Search';

    /**
     * ignore_errors can help debug – remove for production
     * @var bool
     */
    protected $ignoreErrors = true;

    /**
     * BingSearch constructor
     * @param string $accountKey - Microsoft Azure Marketplace AccountKey
     * @param string $searchType - BingSearch API Service Operation
     * @param array $options - BingSearch API Options parameter
     * @param array $webOptions - BingSearch API WebSearchOptions parameter
     * @throws Error
     */
    public function __construct($accountKey, $searchType = self::TYPE_WEB, $options = [], $webOptions = [])
    {
        if (!$accountKey || !is_string($accountKey)) {
            throw new Error('Empty or incorrect Account key');
        }
        $this->accountKey = $accountKey;
        $this->searchType = $searchType;

        if (is_array($options)) {
            $this->setOptions($options);
        }

        if (is_array($webOptions)) {
            $this->setWebOptions($options);
        }
    }

    /**
     * Execute BingSearch
     * @param $query - Bing Search query. The query can contain any valid query text that the Bing Engine supports
     * @param int $top - Number of results to return. The default is 50
     * @param int $skip - Offset requested for the starting point of returned results. The default is 0
     * @param string $format - Type of result
     * @return array|Response|\SimpleXMLElement
     * @throws Error
     */
    public function search($query, $top = 50, $skip = 0, $format = self::FORMAT_OBJECT)
    {
        if (!in_array($format, [self::FORMAT_JSON, self::FORMAT_OBJECT, self::FORMAT_XML])) {
            throw new Error('Unsupported result format');
        }

        $query = urlencode("'" . $query . "'");
        $requestUri = "{$this->uri}/{$this->searchType}?";

        if ($format == self::FORMAT_OBJECT) {
            $requestUri .= "\$format=JSON";
        } else {
            $requestUri .= "\$format={$format}";
        }

        $requestUri .= "&Query={$query}{$this->options}{$this->webOptions}&\$top={$top}&\$skip={$skip}";

        $auth = base64_encode("{$this->accountKey}:{$this->accountKey}");
        $data = array(
            'http' => array(
                'request_fulluri' => true,
                'ignore_errors' => $this->ignoreErrors,
                'header' => "Authorization: Basic $auth"
            )
        );

        $startTime = microtime(true);
        $context = stream_context_create($data);
        $response = file_get_contents($requestUri, 0, $context);
        $endTime = microtime(true) - $startTime;

        if ($format == self::FORMAT_OBJECT || $format == self::FORMAT_JSON) {
            $result = json_decode($response, true);
            if (is_array($result)) {
                if ($format == self::FORMAT_JSON) {
                    return $result;
                } else {
                    return new Response($result, $endTime);
                }
            } else {
                throw new Error($response);
            }
        } else {
            libxml_use_internal_errors(true);
            $result = simplexml_load_string($response);

            if (!$result) {
                throw new Error($response);
            }
            return $result;
        }
    }

    /**
     * @param boolean $ignoreErrors
     */
    public function setIgnoreErrors($ignoreErrors)
    {
        $this->ignoreErrors = (bool)$ignoreErrors;
    }

    protected function setOptions($options)
    {
        $str = "";

        $options = array_filter($options, function ($var) {
            return in_array($var, $this->optionsList);
        });

        if ($options) {
            $str = '&Options=' . urlencode("'" . implode('+', $options) . "'");
        }

        $this->options = $str;
    }

    protected function setWebOptions($options)
    {
        $str = "";

        $options = array_filter($options, function ($var) {
            return in_array($var, $this->webOptionsList);
        });

        if ($options) {
            $str = '&WebSearchOptions=' . urlencode("'" . implode('+', $options) . "'");
        }

        $this->webOptions = $str;
    }
}