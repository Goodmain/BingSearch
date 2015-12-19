<?php

namespace Goodmain\BingSearch\Objects;

class Result
{
    protected $rawData = [];

    /**
     * @var string
     */
    protected $id;
    /**
     * @var string
     */
    protected $title;
    /**
     * @var string
     */
    protected $description;
    /**
     * @var string
     */
    protected $url;
    /**
     * @var string
     */
    protected $displayUrl;

    /**
     * Result constructor.
     * @param array $data
     */
    public function __construct($data)
    {
        $this->rawData = $data;
        $this->extract($data);
    }

    /**
     * @param array $data
     */
    protected function extract($data)
    {
        foreach ($data as $key => $value) {
            if (method_exists(__CLASS__, "set{$key}")) {
                $this->{"set{$key}"}($value);
            }
        }
    }

    /**
     * @return array
     */
    public function getRawData()
    {
        return $this->rawData;
    }

    /**
     * @return string
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setID($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $description = preg_replace('/\x{E000}/u', '<b>', $description);
        $description = preg_replace('/\x{E001}/u', '</b>', $description);
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getDisplayUrl()
    {
        return $this->displayUrl;
    }

    /**
     * @param string $displayUrl
     */
    public function setDisplayUrl($displayUrl)
    {
        $this->displayUrl = $displayUrl;
    }
}