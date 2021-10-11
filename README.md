# Rollun-Usps 3.0.0

## Getting Started
Библиотека предоставляет возможность получать стоимость для разных способов доставки. В библиотеку интегрировано USPS API, но все исчисления производяться самой библиотекой.

Для работы некоторых классов нужно определить PSR-3 логгер, который будет доступен в контейнере
под именем `Psr\Log\LoggerInterface`.

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

### Получение названия зоны доставки с помощью USPS API

Метод взят с страницы https://postcalc.usps.com/DomesticZoneChart (вторая вкладка). Метод позволяет получить зону в которой находится zip получателя относительно zip отправителя.

#### Пример curl запроса

```sh
curl 'https://postcalc.usps.com/DomesticZoneChart/GetZone?origin=10001&destination=10001&shippingDate=12%2F10%2F2020&_=1607610825151' \
-X 'GET' \
-H 'Accept: application/json, text/javascript, */*; q=0.01' \
-H 'Accept-Language: en-gb' \
-H 'Host: postcalc.usps.com' \
-H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.1.3 Safari/605.1.15' \
-H 'Referer: https://postcalc.usps.com/DomesticZoneChart' \
-H 'Accept-Encoding: gzip, deflate, br' \
-H 'Connection: keep-alive' \
-H 'X-Requested-With: XMLHttpRequest'
```

Обязательные параметры
* origin - zip отправителя
* destination - zip получателя
* shippingDate - дата отправки
* _ - текущий timestamp с миллисекундами (пример - 1607610825151). Без этого параметра запрос работает, но сайт USPS его шлет.

Код ответа всегда ожидается 200, даже при неправильных параметрах.

Пример ответа - 

```json5
{
  "OriginError":"",
  "DestinationError":"",
  "ShippingDateError":"",
  "PageError":"",
  "EffectiveDate":"December 1, 2020",
  "ZoneInformation":"The Zone is 1. This is a Local zone. The destination ZIP Code is within the same NDC as the origin ZIP Code."
}
```
ZoneInformation - Название зоны всегда будет первым предложением и будет в формате (по крайней мере судя по тестам).
```
The Zone is <zone_number>
```

Пример ответа с ошибкой, при не указаной дате - 

```json5
{
  "OriginError":"",
  "DestinationError":"",
  "ShippingDateError":"Mailing date is invalid.",
  "PageError":"",
  "EffectiveDate":"",
  "ZoneInformation":""
}
```

### USPS_PACKAGE_COSTS Generator 
Скрипт для генерации массивов с ценами коробок под размер `bin/UspsPackageCostsGenerator.php`
На вох даем csv в формате выходящего массива (примеры есть в data директории), на выход он печатает нужным массив который потом вставляем в класс. 