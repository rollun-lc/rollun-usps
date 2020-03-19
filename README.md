## Getting Started
Библиотека предоставляет возможность получать стоимость для разных способов доставки. В библиотеку интегрировано USPS API, но все исчисления производяться самой библиотекой.

Библиотека добавляет **shipping-all-costs** DataStore для удобного получения информации.
 
**Пример запроса:**

`http://SOME_URL/api/datastore/shipping-all-costs?and(eq(ZipOrigination,10005),eq(ZipDestination,91730),eq(Width,2),eq(Length,2),eq(Height,5),eq(Pounds,2),ne(cost,null()))&sort(+cost)&limit(50)`

### Способы доставки ###
- USPS First-Class Package
- USPS PriorityMail FlatRate
- USPS PriorityMail RegionalRate
- USPS PriorityMail Regular
- Fixed Price

### Добавление собственного способа доставки (Fixed Price)  
```php
<?php
use rollun\Entity\Product\Container\Box; 
use rollun\Entity\Shipping\Method\FixedPrice;
use service\Entity\Rollun\Shipping\Method\Provider\Root;

return [
   'ShippingMethod' => [
       'Root' => [
           'class' => Root::class,
           'shortName' => 'Root',
           'shippingMethodList' => [  // здесь могут быть как shippingMethod так и shippingMethodSubProvider
              'RmPrepCntr',
              'FixedPrice1'
           ]
       ],
       'FixedPrice1' => [
           'class'            => FixedPrice::class,
              'shortName'        => 'FP1',
              'price'            => 8,
              'maxWeight'        => 20,
              'containerService' => 'Fixed Price Box 1'
       ],
   ],
   'Container' => [
       'Fixed Price Box 1' => [
          'class'  => Box::class,
          'Length' => 10,
          'Width'  => 10,
          'Height' => 10
       ],
   ]
];
```

### API USPS
Для работы с API USPS нужно указать **USPS_API_PASS** в .env
``USPS_API_PASS="112233445566"``

Библиотека проводит калькулацию данных самостоятельно, вместе с тем есть возможность проверить совпадают ли внутренняя калькуляция с ответом с API. Для этого нужно просто запустить **unit tests**. 
Для более детального изучения смотрите [phpunit](test/unit/RollunEntity/Shipping/Method/Usps)
