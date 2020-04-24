<?php
declare(strict_types = 1);

namespace NepadaTests\FormRenderer\Filters;

use Latte;
use Mockery\MockInterface;
use Nepada;
use Nette;
use Tester;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class SafeTranslateFilterTest extends Tester\TestCase
{

    /**
     * @dataProvider getNoTranslatorData
     * @param mixed[] $inputArguments
     * @param mixed $expectedTranslation
     */
    public function testWithNoTranslator(array $inputArguments, $expectedTranslation): void
    {
        $filter = new Nepada\FormRenderer\Filters\SafeTranslateFilter(null);
        $filterInfo = new Latte\Runtime\FilterInfo();
        Assert::same($expectedTranslation, $filter->__invoke($filterInfo, ...$inputArguments));
    }

    /**
     * @return mixed[]
     */
    protected function getNoTranslatorData(): array
    {
        $latteHtml = new Latte\Runtime\Html('<div>foo</div>');
        $netteHtml = Nette\Utils\Html::el('div', 'foo');
        return [
            [
                'inputArguments' => [],
                'expectedTranslation' => null,
            ],
            [
                'inputArguments' => ['message'],
                'expectedTranslation' => 'message',
            ],
            [
                'inputArguments' => [1, 2, 3],
                'expectedTranslation' => 1,
            ],
            [
                'inputArguments' => [$netteHtml],
                'expectedTranslation' => $netteHtml,
            ],
            [
                'inputArguments' => [$latteHtml],
                'expectedTranslation' => $latteHtml,
            ],
        ];
    }

    /**
     * @dataProvider getTranslatorData
     * @param mixed[] $inputArguments
     * @param mixed $expectedTranslation
     */
    public function testWithTranslator(array $inputArguments, $expectedTranslation): void
    {
        $filter = new Nepada\FormRenderer\Filters\SafeTranslateFilter($this->mockTranslator());
        $filterInfo = new Latte\Runtime\FilterInfo();
        Assert::same($expectedTranslation, $filter->__invoke($filterInfo, ...$inputArguments));
    }

    /**
     * @return mixed[]
     */
    protected function getTranslatorData(): array
    {
        $latteHtml = new Latte\Runtime\Html('<div>foo</div>');
        $netteHtml = Nette\Utils\Html::el('div', 'foo');
        return [
            [
                'inputArguments' => ['message'],
                'expectedTranslation' => 'translated: ["message"]',
            ],
            [
                'inputArguments' => [1, 2, 3],
                'expectedTranslation' => 'translated: [1,2,3]',
            ],
            [
                'inputArguments' => [$netteHtml],
                'expectedTranslation' => $netteHtml,
            ],
            [
                'inputArguments' => [$latteHtml],
                'expectedTranslation' => $latteHtml,
            ],
        ];
    }

    /**
     * @return Nette\Localization\ITranslator|MockInterface
     */
    private function mockTranslator(): Nette\Localization\ITranslator
    {
        $mock = \Mockery::mock(Nette\Localization\ITranslator::class);
        $mock->shouldReceive('translate')->andReturnUsing(fn (...$args): string => 'translated: ' . Nette\Utils\Json::encode($args));

        return $mock;
    }

}


(new SafeTranslateFilterTest())->run();
