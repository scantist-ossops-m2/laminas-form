<?php

declare(strict_types=1);

namespace LaminasTest\Form\Element;

use DateInterval;
use DateTime;
use Laminas\Filter\DateTimeFormatter;
use Laminas\Form\Element\DateTime as DateTimeElement;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Validator\Date;
use Laminas\Validator\DateStep;
use Laminas\Validator\GreaterThan;
use Laminas\Validator\LessThan;
use PHPUnit\Framework\TestCase;

final class DateTimeTest extends TestCase
{
    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes(): void
    {
        $element = new DateTimeElement('foo');
        $element->setAttributes([
            'inclusive' => true,
            'min'       => '2000-01-01T00:00Z',
            'max'       => '2001-01-01T00:00Z',
            'step'      => '1',
        ]);

        $inputSpec = $element->getInputSpecification();
        self::assertArrayHasKey('validators', $inputSpec);
        self::assertIsArray($inputSpec['validators']);

        $expectedClasses = [
            Date::class,
            GreaterThan::class,
            LessThan::class,
            DateStep::class,
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = $validator::class;
            self::assertContains($class, $expectedClasses, $class);
            switch ($class) {
                case GreaterThan::class:
                    self::assertTrue($validator->getInclusive());
                    self::assertEquals('2000-01-01T00:00Z', $validator->getMin());
                    break;
                case LessThan::class:
                    self::assertTrue($validator->getInclusive());
                    self::assertEquals('2001-01-01T00:00Z', $validator->getMax());
                    break;
                case DateStep::class:
                    $dateInterval = new DateInterval('PT1M');
                    self::assertEquals($dateInterval, $validator->getStep());
                    break;
                default:
                    break;
            }
        }
    }

    public function testProvidesInputSpecificationThatIncludesDateTimeFormatterBasedOnAttributes(): void
    {
        $element = new DateTimeElement('foo');
        $element->setFormat(DateTime::W3C);

        $inputSpec = $element->getInputSpecification();
        self::assertArrayHasKey('filters', $inputSpec);
        self::assertIsArray($inputSpec['filters']);

        foreach ($inputSpec['filters'] as $filter) {
            switch ($filter['name']) {
                case DateTimeFormatter::class:
                    self::assertEquals($filter['options']['format'], DateTime::W3C);
                    self::assertEquals($filter['options']['format'], $element->getFormat());
                    break;
                default:
                    break;
            }
        }
    }

    public function testUsesBrowserFormatByDefault(): void
    {
        $element = new DateTimeElement('foo');
        self::assertEquals('Y-m-d\TH:iP', $element->getFormat());
    }

    public function testSpecifyingADateTimeValueWillReturnBrowserFormattedStringByDefault(): void
    {
        $date    = new DateTime();
        $element = new DateTimeElement('foo');
        $element->setValue($date);
        self::assertEquals($date->format('Y-m-d\TH:iP'), $element->getValue());
    }

    public function testValueIsFormattedAccordingToFormatInElement(): void
    {
        $date    = new DateTime();
        $element = new DateTimeElement('foo');
        $element->setFormat($date::RFC2822);
        $element->setValue($date);
        self::assertEquals($date->format($date::RFC2822), $element->getValue());
    }

    public function testCanRetrieveDateTimeObjectByPassingBooleanFalseToGetValue(): void
    {
        $date    = new DateTime();
        $element = new DateTimeElement('foo');
        $element->setValue($date);
        self::assertSame($date, $element->getValue(false));
    }

    public function testSetFormatWithOptions(): void
    {
        $format  = 'Y-m-d';
        $element = new DateTimeElement('foo');
        $element->setOptions([
            'format' => $format,
        ]);

        self::assertSame($format, $element->getFormat());
    }

    public function testFailsWithInvalidMinSpecification(): void
    {
        $element = new DateTimeElement('foo');
        $element->setAttributes([
            'inclusive' => true,
            'min'       => '2000-01-01T00',
            'step'      => '1',
        ]);

        $this->expectException(InvalidArgumentException::class);
        $element->getInputSpecification();
    }

    public function testFailsWithInvalidMaxSpecification(): void
    {
        $element = new DateTimeElement('foo');
        $element->setAttributes([
            'inclusive' => true,
            'max'       => '2001-01-01T00',
            'step'      => '1',
        ]);
        $this->expectException(InvalidArgumentException::class);
        $element->getInputSpecification();
    }
}
