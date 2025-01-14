<?php
declare(strict_types = 1);

namespace NepadaTests\FormRenderer\Filters;

use Nepada\FormRenderer\Filters\ValidationClassFilter;
use NepadaTests\FormRenderer\Filters\Fixtures\CustomControl;
use NepadaTests\FormRenderer\Filters\Fixtures\FillableCustomControl;
use NepadaTests\TestCase;
use Nette\Forms\Control;
use Nette\Forms\Controls\TextInput;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class ValidationClassFilterTest extends TestCase
{

    /**
     * @dataProvider getFilterData
     */
    public function testFilter(string $description, ?string $invalidClass, ?string $validClass, Control $control, ?string $expectedClass): void
    {
        $filter = new ValidationClassFilter($invalidClass, $validClass);
        Assert::same($expectedClass, $filter->__invoke($control), $description);
    }

    /**
     * @return list<mixed[]>
     */
    protected function getFilterData(): array
    {
        return [
            [
                'description' => 'filled control with error',
                'invalidClass' => 'error',
                'validClass' => 'filled',
                'control' => new FillableCustomControl(true, 'foo', ['error']),
                'expectedClass' => 'error',
            ],
            [
                'description' => 'filled control with error',
                'invalidClass' => null,
                'validClass' => 'filled',
                'control' => new FillableCustomControl(true, 'foo', ['error']),
                'expectedClass' => null,
            ],
            [
                'description' => 'isFilled() preference',
                'invalidClass' => 'error',
                'validClass' => 'filled',
                'control' => new FillableCustomControl(false, 'foo'),
                'expectedClass' => null,
            ],
            [
                'description' => 'isFilled() preference',
                'invalidClass' => 'error',
                'validClass' => 'filled',
                'control' => new FillableCustomControl(true, null),
                'expectedClass' => 'filled',
            ],
            [
                'description' => 'null is not filled by fallback logic',
                'invalidClass' => 'error',
                'validClass' => 'filled',
                'control' => new CustomControl(null),
                'expectedClass' => null,
            ],
            [
                'description' => 'empty array is not filled by fallback logic',
                'invalidClass' => 'error',
                'validClass' => 'filled',
                'control' => new CustomControl([]),
                'expectedClass' => null,
            ],
            [
                'description' => 'empty string is not filled by fallback logic',
                'invalidClass' => 'error',
                'validClass' => 'filled',
                'control' => new CustomControl(''),
                'expectedClass' => null,
            ],
            [
                'description' => 'filled by fallback logic',
                'invalidClass' => 'error',
                'validClass' => 'filled',
                'control' => new CustomControl('foo'),
                'expectedClass' => 'filled',
            ],
            [
                'description' => 'filled password input',
                'invalidClass' => 'error',
                'validClass' => 'filled',
                'control' => $this->createFilledPasswordInput(),
                'expectedClass' => null,
            ],
        ];
    }

    private function createFilledPasswordInput(): TextInput
    {
        $input = new TextInput();
        $input->setHtmlType('password');
        $input->setValue('secret');
        return $input;
    }

}


(new ValidationClassFilterTest())->run();
