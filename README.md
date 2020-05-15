## Getting Started
Библиотека предоставляет возможность получать стоимость для разных способов доставки. В библиотеку интегрировано USPS API, но все исчисления производяться самой библиотекой.

### Способы доставки ###
- RM-DS
- RM-PickUp (USPS)
- PU-DS
- PU-PickUp (USPS)
- WPS-DS
- TR-DS
- SLT-DS
- AU-DS
- AU-DS-COVID19


### DataStore: shipping-all-costs
Библиотека предоставляет shipping-all-costs DataStore, который возвращает цены по все объявленным (в конфиге) методам доставки. 

Примеры запросов:
```
http://SOME_URL/api/datastore/shipping-all-costs?and(eq(ZipOrigination,10005),eq(ZipDestination,91730),eq(Width,2),eq(Length,2),eq(Height,5),eq(Pounds,2),ne(cost,null()))&sort(+cost)&limit(50)
http://SOME_URL/api/datastore/shipping-all-costs?and(eq(ZipOrigination,28790),eq(ZipDestination,91730),eq(Width,2),eq(Length,2),eq(Height,1),eq(Pounds,2),eq(attr_CommodityCode,301),ne(cost,null()))&sort(+cost)&limit(50)
http://SOME_URL/api/datastore/shipping-all-costs?ZipOrigination=91601&ZipDestination=91730&Width=1&Length=10&Height=5&Pounds=0.5&Click_N_Shipp=Priority%20Mail
http://SOME_URL/api/datastore/shipping-all-costs?ZipOrigination=91601&ZipDestination=91730&Width=1&Length=10&Height=5&Pounds=1&like(id,*FtCls*)&limit(2,1)&select(id)
```
Есть возможность отправить в методы доставки дополнительные атрибуты. Для этого в запросе укажите ```...,еq(attr_CommodityCode,301),...```, это означает что все методы доставки получат атрибут CommodityCode со значением 301.

### DataStore: best-shipping
Библиотека предоставляет best-shipping DataStore, который возвращает лучший по приоритету способ доставки. Может возвращать несколько способов еслы продукт будет сразу в нескольких поставщиков. 

Схема (логика): https://cawemo.com/share/ddc9e83a-8776-485b-bea9-d111ccd4c9e3


Примеры запросов:
```
http://SOME_URL/api/datastore/best-shipping?and(eq(RollunId,XNU7S),eq(ZipDestination,91731))
```

### Добавление собственного способа доставки
Библиотека предоставляет возможность добавлять собственные способы доставки. Все возможные способы доставки должны быть объявлены в **RootShippingProvider**, так как здесь используется древовидная структура и началом дерево является root. 

Пример того как при помощи конфигураций добавить способ доставки который будет называтся **FixedPrice1**.
```php
<?php
use rollun\Entity\Product\Container\Box; 
use rollun\Entity\Shipping\Method\FixedPrice;
use service\Entity\Rollun\Shipping\Method\Provider\Root as RootShippingProvider;
use rollun\Entity\Shipping\Method\Provider\PickUp\RmPickUp;

return [
   'ShippingMethod' => [
       'Root' => [
           'class' => RootShippingProvider::class, // указываем элемент сервис менеджера
           'shortName' => 'Root', 
           'shippingMethodList' => [
              'RM-PickUp', // добавляем первый способ доставки который по своей сути является еще одним ShippingMethodProvider со своими способами доставки
              'FixedPrice1' // добавляем наш способ доставки
           ]
       ],
       'RM-PickUp' => [ // объявляем ShippingMethodProvider
           'class'                 => RmPickUp::class, // указываем элемент сервис менеджера
              'shortName'          => 'RM-PickUp',
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
Для работы с API USPS нужно указать **USPS_API_PASS** в .env (требуется только в dev режиме)
``USPS_API_PASS="112233445566"``

Библиотека проводит калькулацию данных самостоятельно, вместе с тем есть возможность проверить совпадают ли внутренняя калькуляция с ответом с API. Для этого нужно просто запустить **unit tests**. 
Для более детального изучения смотрите [phpunit](test/unit/RollunEntity/Shipping/Method/Usps)

### Механизм упаковки посылки
Контейнер `rollun\Entity\Product\Container\Box` в методах проверки возможности упаковать посылку в контейнер использует **PackagePacker**.
PackagePacker это микросервис которые предоставляет API для https://github.com/betterwaysystems/packer.

Укажите `PACKAGE_PACKER_API_URL` в env для того чтобы библиотека могла получать данные по API.
