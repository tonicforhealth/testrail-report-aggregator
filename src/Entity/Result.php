<?php

namespace TonicForHealth\ReportAggregator\Entity;

/**
 * Class Result
 */
class Result
{
    const STATUS_PASSED = 1;
    const STATUS_BLOCKED = 2;
    const STATUS_UNTESTED = 3;
    const STATUS_RETEST = 4;
    const STATUS_FAILED = 5;

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $statusId;

    /**
     * @var string
     */
    private $comment;

    public function __construct($statusId = self::STATUS_PASSED)
    {
        $this->setStatusId($statusId);
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
     * @return int
     */
    public function getStatusId()
    {
        return $this->statusId;
    }

    /**
     * @param int $statusId
     */
    public function setStatusId($statusId)
    {
        $this->statusId = $statusId;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }
}
