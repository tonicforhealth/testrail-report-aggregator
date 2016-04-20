<?php

namespace TonicForHealth\ReportAggregator\Entity;

/**
 * Class TestCase
 */
class TestCase
{
    /**
     * @var int;
     */
    private $id;

    /**
     * @var int;
     */
    private $testId;

    /**
     * @var string;
     */
    private $title;

    /**
     * @var ResultCollection;
     */
    private $results;

    /**
     * Cases constructor.
     *
     * @param $title
     * @param ResultCollection|null $results
     */
    public function __construct($title, ResultCollection $results = null)
    {
        $this->setTitle($title);

        if (null === $results) {
            $results = new ResultCollection();
        }

        $this->setResults($results);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
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
     * @return ResultCollection
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @param ResultCollection $results
     */
    public function setResults($results)
    {
        $this->results = $results;
    }

    /**
     * @return int
     */
    public function getTestId()
    {
        return $this->testId;
    }

    /**
     * @param int $testId
     */
    public function setTestId($testId)
    {
        $this->testId = $testId;
    }
}
