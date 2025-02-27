<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlAlphabetical;

use PHPUnit\Framework\TestCase;
use ReflectionClass;

class YamlSortServiceTest extends TestCase
{
    /**
     * @return array
     */
    public static function getDataForAlphabeticalSortProvider(): array
    {
        return [
            [
                'expectedSortedArray' => [
                    'bar' => [
                        'key2' => [
                            'key12' => 'value',
                            'key5' => 'value',
                            'key2' => 'value',
                        ],
                        'key1' => [
                            'key1111' => 'value',
                            'key111' => 'value',
                            'key11' => 'value',
                        ],
                    ],
                    'foo' => 'bar',
                    'qux' => 'baz',
                ],
                'depth' => 1,
                'prioritizedKeys' => [],
            ],
            [
                'expectedSortedArray' => [
                    'bar' => [
                        'key1' => [
                            'key1111' => 'value',
                            'key111' => 'value',
                            'key11' => 'value',
                        ],
                        'key2' => [
                            'key12' => 'value',
                            'key5' => 'value',
                            'key2' => 'value',
                        ],
                    ],
                    'foo' => 'bar',
                    'qux' => 'baz',
                ],
                'depth' => 2,
                'prioritizedKeys' => [],
            ],
            [
                'expectedSortedArray' => [
                    'bar' => [
                        'key1' => [
                            'key11' => 'value',
                            'key111' => 'value',
                            'key1111' => 'value',
                        ],
                        'key2' => [
                            'key2' => 'value',
                            'key5' => 'value',
                            'key12' => 'value',
                        ],
                    ],
                    'foo' => 'bar',
                    'qux' => 'baz',
                ],
                'depth' => 3,
                'prioritizedKeys' => [],
            ],
            [
                'expectedSortedArray' => [
                    'qux' => 'baz',
                    'bar' => [
                        'key1' => [
                            'key11' => 'value',
                            'key111' => 'value',
                            'key1111' => 'value',
                        ],
                        'key2' => [
                            'key2' => 'value',
                            'key5' => 'value',
                            'key12' => 'value',
                        ],
                    ],
                    'foo' => 'bar',
                ],
                'depth' => 3,
                'prioritizedKeys' => ['qux'],
            ],
        ];
    }

    /**
     * @dataProvider getDataForAlphabeticalSortProvider
     * @param array $expectedSortedArray
     * @param int $depth
     * @param string[] $prioritizedKeys
     */
    public function testSortArrayByDepth(array $expectedSortedArray, int $depth, array $prioritizedKeys): void
    {
        /** @var string[] $array */
        $array = [
            'qux' => 'baz',
            'foo' => 'bar',
            'bar' => [
                'key2' => [
                    'key12' => 'value',
                    'key5' => 'value',
                    'key2' => 'value',
                ],
                'key1' => [
                    'key1111' => 'value',
                    'key111' => 'value',
                    'key11' => 'value',
                ],
            ],
        ];
        $actualSortedArray = YamlSortService::sortArray($array, $depth, $prioritizedKeys);

        $this->assertSame($expectedSortedArray, $actualSortedArray);
    }

    /**
     * @return array
     */
    public static function getArrayDataAndPrioritizedKeysProvider(): array
    {
        return [
            [
                'array' => [
                    'foo' => 'value',
                    'bar' => 'value',
                    'baz' => 'value',
                    'qux' => 'value',
                    'quux' => 'value',
                    'quuz' => 'value',
                    'corge' => 'value',
                    'grault' => 'value',
                ],
                'expectedSortedArray' => [
                    'quuz' => 'value',
                    'foo' => 'value',
                    'bar' => 'value',
                    'baz' => 'value',
                    'qux' => 'value',
                    'quux' => 'value',
                    'corge' => 'value',
                    'grault' => 'value',
                ],
                'prioritizedKey' => [
                    'quuz',
                ],
            ],
            [
                'array' => [
                    'foo' => 'value',
                    'bar' => 'value',
                    'baz' => 'value',
                    'qux' => 'value',
                    'quux' => 'value',
                    'quuz' => 'value',
                    'corge' => 'value',
                    'grault' => 'value',
                ],
                'expectedSortedArray' => [
                    'corge' => 'value',
                    'quux' => 'value',
                    'grault' => 'value',
                    'foo' => 'value',
                    'bar' => 'value',
                    'baz' => 'value',
                    'qux' => 'value',
                    'quuz' => 'value',
                ],
                'prioritizedKey' => [
                    'corge', 'quux', 'grault',
                ],
            ],
            [
                'array' => [
                    'foo' => 'value',
                    'bar' => 'value',
                    'baz' => 'value',
                    'fooqux' => 'value',
                    'quux' => 'value',
                    'quuz' => 'value',
                    'corge' => 'value',
                    'grault' => 'value',
                ],
                'expectedSortedArray' => [
                    'foo' => 'value',
                    'fooqux' => 'value',
                    'bar' => 'value',
                    'baz' => 'value',
                    'quux' => 'value',
                    'quuz' => 'value',
                    'corge' => 'value',
                    'grault' => 'value',
                ],
                'prioritizedKey' => [
                    'foo',
                ],
            ],
            [
                'array' => [
                    'foo:key:' => 'value',
                    'foobar:key:' => 'value',
                    'foobaz:key:' => 'value',
                    'fooqux:key:' => 'value',
                    'fooquux:key:' => 'value',
                    'fooquuz:key:' => 'value',
                    'foocorge:key:' => 'value',
                    'foograult:key:' => 'value',
                ],
                'expectedSortedArray' => [
                    'foo:key:' => 'value',
                    'foograult:key:' => 'value',
                    'foobar:key:' => 'value',
                    'foobaz:key:' => 'value',
                    'fooqux:key:' => 'value',
                    'fooquux:key:' => 'value',
                    'fooquuz:key:' => 'value',
                    'foocorge:key:' => 'value',
                ],
                'prioritizedKey' => [
                    'foo::exact', 'grault',
                ],
            ],
        ];
    }

    /**
     * @dataProvider getArrayDataAndPrioritizedKeysProvider
     * @param string[] $array
     * @param string[] $expectedSortedArray
     * @param string[] $prioritizedKey
     */
    public function testSortArrayByPrioritizedKeys(array $array, array $expectedSortedArray, array $prioritizedKey): void
    {
        $reflectionClass = new ReflectionClass(YamlSortService::class);
        $reflectionMethod = $reflectionClass->getMethod('sortArrayElementsByPrioritizedKeys');
        $reflectionMethod->setAccessible(true);

        $actualSortedArray = $reflectionMethod->invokeArgs(new YamlSortService(), [$prioritizedKey, $array]);

        $this->assertSame($expectedSortedArray, $actualSortedArray);
    }
}
