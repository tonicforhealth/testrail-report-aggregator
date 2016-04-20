<?php

namespace TonicForHealth\ReportAggregator\Entity;

use Collections\Collection;

/**
 * Class ResultCollection
 */
class ResultCollection extends Collection
{
    const OBJECT_CLASS = Result::class;

    /**
     * ProcessingCheckCollection constructor.
     */
    public function __construct()
    {
        parent::__construct(static::OBJECT_CLASS);
    }
}
