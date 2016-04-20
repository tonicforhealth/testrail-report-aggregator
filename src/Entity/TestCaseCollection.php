<?php

namespace TonicForHealth\ReportAggregator\Entity;

use Collections\Collection;

/**
 * Class TestCaseCollection
 */
class TestCaseCollection extends Collection
{
    const OBJECT_CLASS = TestCase::class;

    /**
     * ProcessingCheckCollection constructor.
     */
    public function __construct()
    {
        parent::__construct(static::OBJECT_CLASS);
    }
}
