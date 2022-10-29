<?php

declare(strict_types=1);

namespace LaminasTest\Form\Element;

use Laminas\Form\Element\Number as NumberElement;
use Laminas\Validator\GreaterThan;
use Laminas\Validator\LessThan;
use Laminas\Validator\Regex;
use Laminas\Validator\Step;
use PHPUnit\Framework\TestCase;

final class NumberTest extends TestCase
{
    public function testProvidesInputSpecificationWithDefaultAttributes(): void
    {
        $element = new NumberElement();

        $inputSpec = $element->getInputSpecification();
        self::assertArrayHasKey('validators', $inputSpec);
        self::assertIsArray($inputSpec['validators']);

        $expectedClasses = [
            Regex::class,
            Step::class,
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = $validator::class;
            self::assertContains($class, $expectedClasses, $class);
            switch ($class) {
                case Step::class:
                    self::assertEquals(1, $validator->getStep());
                    break;
                default:
                    break;
            }
        }
    }

    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes(): void
    {
        $element = new NumberElement();
        $element->setAttributes([
            'inclusive' => true,
            'min'       => 5,
            'max'       => 10,
            'step'      => 1,
        ]);

        $inputSpec = $element->getInputSpecification();
        self::assertArrayHasKey('validators', $inputSpec);
        self::assertIsArray($inputSpec['validators']);

        $expectedClasses = [
            GreaterThan::class,
            LessThan::class,
            Regex::class,
            Step::class,
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = $validator::class;
            self::assertContains($class, $expectedClasses, $class);
            switch ($class) {
                case GreaterThan::class:
                    self::assertTrue($validator->getInclusive());
                    self::assertEquals(5, $validator->getMin());
                    break;
                case LessThan::class:
                    self::assertTrue($validator->getInclusive());
                    self::assertEquals(10, $validator->getMax());
                    break;
                case Step::class:
                    self::assertEquals(1, $validator->getStep());
                    break;
                default:
                    break;
            }
        }
    }

    public function testFalseInclusiveValidatorBasedOnAttributes(): void
    {
        $element = new NumberElement();
        $element->setAttributes([
            'inclusive' => false,
            'min'       => 5,
        ]);

        $inputSpec = $element->getInputSpecification();
        foreach ($inputSpec['validators'] as $validator) {
            if ($validator::class === GreaterThan::class) {
                self::assertFalse($validator->getInclusive());
                break;
            }
        }
    }

    public function testDefaultInclusiveTrueatValidatorWhenInclusiveIsNotSetOnAttributes(): void
    {
        $element = new NumberElement();
        $element->setAttributes([
            'min' => 5,
        ]);

        $inputSpec = $element->getInputSpecification();
        foreach ($inputSpec['validators'] as $validator) {
            if ($validator::class === GreaterThan::class) {
                self::assertTrue($validator->getInclusive());
                break;
            }
        }
    }

    public function testOnlyCastableDecimalsAreAccepted(): void
    {
        $element = new NumberElement();

        $inputSpec = $element->getInputSpecification();
        foreach ($inputSpec['validators'] as $validator) {
            if ($validator::class === Regex::class) {
                self::assertFalse($validator->isValid('1,000.01'));
                self::assertFalse($validator->isValid('-1,000.01'));
                self::assertTrue($validator->isValid('1000.01'));
                self::assertTrue($validator->isValid('-1000.01'));
                break;
            }
        }
    }
}
