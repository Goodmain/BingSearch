<?php

namespace Goodmain\BingSearch\Objects;

class Response
{
    /**
     * @var array
     */
    protected $results = [];
    /**
     * Count of results
     * @var int
     */
    protected $count = 0;
    /**
     * Time spent on request
     * @var float
     */
    protected $time = 0;
    /**
     * Uri for the next page
     * @var string
     */
    protected $next = '';


    public function __construct($data, $time = 0)
    {
        if (!empty($data['d']) && !empty($data['d']['results'])) {
            $this->count = count($data['d']['results']);

            $this->extract($data['d']['results']);
        }
        if (!empty($data['d']) && !empty($data['d']['__next'])) {
            $this->next = $data['d']['__next'];
        }
        $this->time = $time;
    }

    /**
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @return float
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @return string
     */
    public function getNext()
    {
        return $this->next;
    }

    protected function extract($data)
    {
        $results = [];
        foreach ($data as $row) {
            $results[] = new Result($row);
        }

        $this->results = $results;
    }
}