<?php

namespace TonicForHealth\ReportAggregator\Report\JUnit;

use DOMDocument;
use DOMXPath;
use TonicForHealth\ReportAggregator\Report\ReportInterface;

/**
 * Class JUnitReport
 */
class JUnitReport implements ReportInterface
{
    const XML_VERSION = '1.0';
    const XML_ENCODING = 'UTF-8';
    const XML_REQUIRE_TAG_NAME = 'testsuites';

    /**
     * @var DOMDocument ;
     */
    private $DOMDocument;

    /**
     * @var DOMXpath;
     */
    private $xpath;

    /**
     * JunitReport constructor.
     *
     * @param string $reportPath
     *
     * @throws JUnitReportInvalidXml
     * @throws JUnitReportNotExist
     */
    public function __construct($reportPath)
    {
        if (!is_readable($reportPath)) {
            throw JUnitReportNotExist::fileNotExist($reportPath);
        }
        $DOMDocument = new DOMDocument(static::XML_VERSION, static::XML_ENCODING);

        if (!@$DOMDocument->load($reportPath)) {
            throw JUnitReportInvalidXml::invalidJUnitXml($reportPath);
        }
        $MainNode = $DOMDocument->getElementsByTagName(static::XML_REQUIRE_TAG_NAME);

        if ($MainNode->length <= 0) {
            throw JUnitReportInvalidXml::invalidJUnitXml($reportPath);
        }

        $this->setDOMDocument($DOMDocument);

        $this->setXpath(new DOMXPath($DOMDocument));
    }

    /**
     * @return DOMDocument
     */
    public function getDOMDocument()
    {
        return $this->DOMDocument;
    }

    /**
     * @return DOMXPath
     */
    public function getXpath()
    {
        return $this->xpath;
    }

    /**
     * @param DOMDocument $DOMDocument
     */
    protected function setDOMDocument(DOMDocument $DOMDocument)
    {
        $this->DOMDocument = $DOMDocument;
    }

    /**
     * @param DOMXPath $xpath
     */
    protected function setXpath(DOMXPath $xpath)
    {
        $this->xpath = $xpath;
    }
}
