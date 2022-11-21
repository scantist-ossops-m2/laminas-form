<?php

declare(strict_types=1);

namespace LaminasTest\Form\View\Helper;

use Laminas\Form\Element;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\View\Helper\FormDateTimeLocal as FormDateTimeLocalHelper;

use function sprintf;

/**
 * @property FormDateTimeLocalHelper $helper
 */
final class FormDateTimeLocalTest extends AbstractCommonTestCase
{
    protected function setUp(): void
    {
        $this->helper = new FormDateTimeLocalHelper();
        parent::setUp();
    }

    public function testRaisesExceptionWhenNameIsNotPresentInElement(): void
    {
        $element = new Element();
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('name');
        $this->helper->render($element);
    }

    public function testGeneratesInputTagWithElement(): void
    {
        $element = new Element('foo');
        $markup  = $this->helper->render($element);
        self::assertStringContainsString('<input ', $markup);
        self::assertStringContainsString('type="datetime-local"', $markup);
    }

    public function testGeneratesInputTagRegardlessOfElementType(): void
    {
        $element = new Element('foo');
        $element->setAttribute('type', 'email');
        $markup = $this->helper->render($element);
        self::assertStringContainsString('<input ', $markup);
        self::assertStringContainsString('type="datetime-local"', $markup);
    }

    public function validAttributes(): array
    {
        return [
            ['name',           'assertStringContainsString'],
            ['accept',         'assertStringNotContainsString'],
            ['alt',            'assertStringNotContainsString'],
            ['autocomplete',   'assertStringContainsString'],
            ['autofocus',      'assertStringContainsString'],
            ['checked',        'assertStringNotContainsString'],
            ['dirname',        'assertStringNotContainsString'],
            ['disabled',       'assertStringContainsString'],
            ['form',           'assertStringContainsString'],
            ['formaction',     'assertStringNotContainsString'],
            ['formenctype',    'assertStringNotContainsString'],
            ['formmethod',     'assertStringNotContainsString'],
            ['formnovalidate', 'assertStringNotContainsString'],
            ['formtarget',     'assertStringNotContainsString'],
            ['height',         'assertStringNotContainsString'],
            ['list',           'assertStringContainsString'],
            ['max',            'assertStringContainsString'],
            ['maxlength',      'assertStringNotContainsString'],
            ['min',            'assertStringContainsString'],
            ['multiple',       'assertStringNotContainsString'],
            ['pattern',        'assertStringNotContainsString'],
            ['placeholder',    'assertStringNotContainsString'],
            ['readonly',       'assertStringContainsString'],
            ['required',       'assertStringContainsString'],
            ['size',           'assertStringNotContainsString'],
            ['src',            'assertStringNotContainsString'],
            ['step',           'assertStringContainsString'],
            ['value',          'assertStringContainsString'],
            ['width',          'assertStringNotContainsString'],
        ];
    }

    public function getCompleteElement(): Element
    {
        $element = new Element('foo');
        $element->setAttributes([
            'accept'         => 'value',
            'alt'            => 'value',
            'autocomplete'   => 'on',
            'autofocus'      => 'autofocus',
            'checked'        => 'checked',
            'dirname'        => 'value',
            'disabled'       => 'disabled',
            'form'           => 'value',
            'formaction'     => 'value',
            'formenctype'    => 'value',
            'formmethod'     => 'value',
            'formnovalidate' => 'value',
            'formtarget'     => 'value',
            'height'         => 'value',
            'id'             => 'value',
            'list'           => 'value',
            'max'            => 'value',
            'maxlength'      => 'value',
            'min'            => 'value',
            'multiple'       => 'multiple',
            'name'           => 'value',
            'pattern'        => 'value',
            'placeholder'    => 'value',
            'readonly'       => 'readonly',
            'required'       => 'required',
            'size'           => 'value',
            'src'            => 'value',
            'step'           => 'value',
            'width'          => 'value',
        ]);
        $element->setValue('value');
        return $element;
    }

    /**
     * @dataProvider validAttributes
     */
    public function testAllValidFormMarkupAttributesPresentInElementAreRendered(
        string $attribute,
        string $assertion
    ): void {
        $element = $this->getCompleteElement();
        $markup  = $this->helper->render($element);
        $expect  = match ($attribute) {
            'value' => sprintf('%s="%s"', $attribute, $element->getValue()),
            default => sprintf('%s="%s"', $attribute, $element->getAttribute($attribute)),
        };
        $this->$assertion($expect, $markup);
    }

    public function testInvokeProxiesToRender(): void
    {
        $element = new Element('foo');
        $markup  = $this->helper->__invoke($element);
        self::assertStringContainsString('<input', $markup);
        self::assertStringContainsString('name="foo"', $markup);
        self::assertStringContainsString('type="datetime-local"', $markup);
    }

    public function testInvokeWithNoElementChainsHelper(): void
    {
        self::assertSame($this->helper, $this->helper->__invoke());
    }
}
