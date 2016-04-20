<?php

namespace TonicForHealth\ReportAggregator\Test\Report\JUnit;

use PHPUnit_Framework_Error_Warning;
use PHPUnit_Framework_TestCase;
use TonicForHealth\ReportAggregator\Report\JUnit\JUnitReport;
use TonicForHealth\ReportAggregator\Report\JUnit\JUnitReportInvalidXml;

class JUnitReportTest extends PHPUnit_Framework_TestCase
{
    /**
     * test throw JUnitReportNotExist
     *
     * @expectedException \TonicForHealth\ReportAggregator\Report\JUnit\JUnitReportNotExist
     */
    public function testJUnitReportNotExist()
    {
        new JunitReport('/test/test/test');
    }

    /**
     * test throw JUnitReportInvalidXml load
     *
     * @expectedException \TonicForHealth\ReportAggregator\Report\JUnit\JUnitReportInvalidXml
     */
    public function testJUnitReportInvalidXmlLoad()
    {
        $file = sys_get_temp_dir().DIRECTORY_SEPARATOR.'testJUnit.xml';

        file_put_contents($file, '<?xml version="1.0" encoding="UTF-8"?>');
        try {
            new JunitReport($file);
        } catch (PHPUnit_Framework_Error_Warning $e) {
            throw new JUnitReportInvalidXml($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * test throw JUnitReportInvalidXml require tag
     *
     * @expectedException \TonicForHealth\ReportAggregator\Report\JUnit\JUnitReportInvalidXml
     */
    public function testJUnitReportInvalidXmlRequire()
    {
        $file = sys_get_temp_dir().DIRECTORY_SEPARATOR.'testJUnit.xml';

        file_put_contents($file, '<?xml version="1.0" encoding="UTF-8"?><test></test>');
        new JunitReport($file);
    }

    /**
     * test throw JUnitReportInvalidXml
     */
    public function testOk()
    {
        $file = sys_get_temp_dir().DIRECTORY_SEPARATOR.'testJUnit.xml';

        file_put_contents($file, '<?xml version="1.0" encoding="UTF-8"?><'.JUnitReport::XML_REQUIRE_TAG_NAME.'/>');

        new JUnitReport($file);
    }
}
