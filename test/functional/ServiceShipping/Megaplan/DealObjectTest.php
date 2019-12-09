<?php
/**
 * Created by PhpStorm.
 * User: itprofessor02
 * Date: 02.04.19
 * Time: 13:02
 */

namespace rollun\test\functional\Shipping\Megaplan;

use PHPUnit\Framework\TestCase;
use service\Shipping\Megaplan\DealObject;

/**
 * Class DealObjectTest
 */
class DealObjectTest extends TestCase
{
    private $dealData = [
        'Id' => '41214',
        'GUID' => '',
        'Name' => '№4961',
        'Program' => ['Id' => 12, 'Name' => 'Ebay_Plaisir_DropShipOrders'],
        'ProgramId' => 12,
    ];

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidDeal()
    {
        new DealObject([]);
    }

    public function successSetDataProvider()
    {
        return [
            [
                'IsPaid',
                true,
                [
                    'IsPaid' => false
                ]
            ],
            [
                'Category1000059CustomFieldOtpravitel',
                'Rockymountain',
                [
                    'Category1000059CustomFieldOtpravitel' => 'Rockymountain',
                ]
            ],
            [
                'Otpravitel',
                'Rockymountain',
                [
                    'Category1000059CustomFieldOtpravitel' => 'Rockymountain',
                ]
            ],
            [
                'ZakupochnayaTsena',
                [
                    'Value' => 84.74,
                    'Currency' => 'грн.',
                    'CurrencyId' => 1000000,
                    'CurrencyAbbreviation' => 'UAH',
                    'Rate' => 1
                ],
                [
                    'Category1000059CustomFieldZakupochnayaTsena' => [
                        'Value' => 60.74,
                        'Currency' => 'грн.',
                        'CurrencyId' => 1000000,
                        'CurrencyAbbreviation' => 'UAH',
                        'Rate' => 1
                    ],
                ]
            ],
            [
                'ZakupochnayaTsena',
                [
                    'Value' => 35.74,
                ],
                [
                    'Category1000059CustomFieldZakupochnayaTsena' => [
                        'Value' => 60.74,
                        'Currency' => 'грн.',
                        'CurrencyId' => 1000000,
                        'CurrencyAbbreviation' => 'UAH',
                        'Rate' => 1
                    ],
                ]
            ],
        ];
    }

    /**
     * @param $field
     * @param $value
     * @param array $addFields
     * @dataProvider successSetDataProvider
     */
    public function testSuccessSet($field, $value, array $addFields = [])
    {
        $deal = new DealObject(array_merge($this->dealData, $addFields));
        $deal->set($field, $value);
        $this->assertEquals($deal->get($field), $value);
    }


    public function failureSetDataProvider()
    {
        return [
            [
                'IsPaid',
                true,
            ],
            [
                'Id',
                23,
            ]
        ];
    }

    /**
     * @param $field
     * @param $value
     * @param array $addFields
     * @dataProvider failureSetDataProvider
     * @expectedException \InvalidArgumentException
     */
    public function testFailureSet($field, $value, array $addFields = [])
    {
        $deal = new DealObject(array_merge($this->dealData, $addFields));
        $deal->set($field, $value);
    }

    public function failureGetDataProvider()
    {
        return [
            [
                'IsPaid',
            ],
            [
                'Tsena',
                [
                    'Category1000059CustomFieldZakupochnayaTsena' => [
                        'Value' => 60.74,
                        'Currency' => 'грн.',
                        'CurrencyId' => 1000000,
                        'CurrencyAbbreviation' => 'UAH',
                        'Rate' => 1
                    ],
                ]
            ]
        ];
    }

    /**
     * @param $field
     * @param array $addFields
     * @expectedException \InvalidArgumentException
     * @dataProvider failureGetDataProvider
     */
    public function testFailureGet($field, array $addFields = [])
    {
        $deal = new DealObject(array_merge($this->dealData, $addFields));
        $deal->get($field);
    }


    public function successToArrayDataProvider()
    {
        return [
            [
                [],
                [],
                false,
                array_merge($this->dealData, [])
            ],
            [
                [
                    'IsPaid' => false,
                    'Category1000059CustomFieldOtpravitel' => 'Rockymountain',
                ],
                [

                ],
                false,
                array_merge($this->dealData, [
                    'IsPaid' => false,
                    'Model' => [
                        'Category1000059CustomFieldOtpravitel' => 'Rockymountain',
                    ]
                ])
            ],
            [
                [
                    'IsPaid' => false,
                    'Category1000059CustomFieldOtpravitel' => 'Rockymountain',
                ],
                [
                    'Category1000059CustomFieldOtpravitel' => 'Jeff',
                ],
                true,
                [
                    'Id' => $this->dealData['Id'],
                    'Model' => [
                        'Category1000059CustomFieldOtpravitel' => 'Jeff',
                    ]
                ],
            ],
            [
                [
                    'IsPaid' => false,
                    'Category1000059CustomFieldOtpravitel' => 'Rockymountain',
                ],
                [
                    'Category1000059CustomFieldOtpravitel' => 'Jeff',
                    'IsPaid' => true,

                ],
                true,
                [
                    'Id' => $this->dealData['Id'],
                    'IsPaid' => true,
                    'Model' => [
                        'Category1000059CustomFieldOtpravitel' => 'Jeff',
                    ]
                ],
            ],
            [
                [
                    'IsPaid' => false
                ],
                [],
                false,
                array_merge($this->dealData, [
                    'IsPaid' => false
                ])
            ],
            [
                [
                    'IsPaid' => false
                ],
                [],
                true,
                [
                    'Id' => $this->dealData['Id']
                ]
            ],
            [
                [
                    'IsPaid' => false
                ],
                [
                    'IsPaid' => true
                ],
                true,
                [
                    'Id' => $this->dealData['Id'],
                    'IsPaid' => true
                ]
            ],
        ];
    }

    /**
     * @param array $addFields
     * @param array $changedFields
     * @param $onlyChanged
     * @param array $expectedOutputArray
     * @dataProvider successToArrayDataProvider
     */
    public function testSuccessToArray(array $addFields, array $changedFields, $onlyChanged, array $expectedOutputArray)
    {
        $deal = new DealObject(array_merge($this->dealData, $addFields));
        foreach ($changedFields as $changedField => $changedValue) {
            $deal->set($changedField, $changedValue);
        }
        $this->assertEquals($expectedOutputArray, $deal->toArray($onlyChanged));
    }
}
