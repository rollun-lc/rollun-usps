<?php


namespace rollun\Service\Products;


use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverBrowserType;

class WebDriverTest
{
    public function __invoke()
    {
        /** @var DesiredCapabilities $capability */
        try {
            $capability = call_user_func([DesiredCapabilities::class, WebDriverBrowserType::CHROME]);

            $capability->setCapability('enableVNC', true);

            $webDriver = RemoteWebDriver::create(
                'http://selenoid-proxy:4444/wd/hub/',
                $capability,
                15000,
                15000
            );
            $webDriver->get('https://www.google.com/');
            sleep(240);
        } finally {
            $webDriver->quit();
        }
    }
}