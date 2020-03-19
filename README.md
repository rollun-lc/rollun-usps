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

### Добавление собственного способа доставки
Библиотека предоставляет возможность добавлять собственные способы доставки. Все возможные способы доставки должны быть объявлены в **RootShippingProvider**, так как здесь используется древовидная структура и началом дерево является root. 

Пример того как при помощи конфигураций добавить способ доставки который будет называтся **FixedPrice1**.
```php
<?php
use rollun\Entity\Product\Container\Box; 
use rollun\Entity\Shipping\Method\FixedPrice;
use service\Entity\Rollun\Shipping\Method\Provider\Root as RootShippingProvider;
use service\Entity\Rollun\Shipping\Method\Provider\RmPrepCenter;

return [
   'ShippingMethod' => [
       'Root' => [
           'class' => RootShippingProvider::class, // указываем элемент сервис менеджера
           'shortName' => 'Root', 
           'shippingMethodList' => [
              'RmPrepCntr', // добавляем первый способ доставки который по своей сути является еще одним ShippingMethodProvider со своими способами доставки
              'FixedPrice1' // добавляем наш способ доставки
           ]
       ],
       'RmPrepCntr' => [ // объявляем ShippingMethodProvider
           'class'                 => RmPrepCenter::class, // указываем элемент сервис менеджера
              'shortName'          => 'RmPrepCntr',
              'shippingMethodList' => [
                 'Usps' // указываем способы доставки
              ]
       ],
       'FixedPrice1' => [ // добавляем наш способ доставки
           'class'            => FixedPrice::class, // указываем элемент сервис менеджера
              'shortName'        => 'FP1',
              'price'            => 8,  // характерно только для FixedPrice::class, указываем цену
              'maxWeight'        => 20, // характерно только для FixedPrice::class, максимально допустимый вес
              'containerService' => 'Fixed Price Box 1' // // характерно только для FixedPrice::class, указываем контейнер
       ],
   ],
   'Container' => [
       'Fixed Price Box 1' => [ // объявляем контейнер
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
