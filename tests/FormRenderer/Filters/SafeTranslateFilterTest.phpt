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
     */
    public function testWithNoTranslator(array $inputArguments, mixed $expectedTranslation): void
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
     */
    public function testWithTranslator(array $inputArguments, mixed $expectedTranslation): void
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
                'inputArguments' => ['1', 2, 3],
                'expectedTranslation' => 'translated: ["1",2,3]',
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
     * @return Nette\Localization\Translator|MockInterface
     */
    private function mockTranslator(): Nette\Localization\Translator
    {
        $mock = \Mockery::mock(Nette\Localization\Translator::class);
        $mock->shouldReceive('translate')->andReturnUsing(fn (mixed ...$args): string => 'translated: ' . Nette\Utils\Json::encode($args));

        return $mock;
    }

}


(new SafeTranslateFilterTest())->run();
