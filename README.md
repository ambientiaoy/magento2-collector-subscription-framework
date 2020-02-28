# Collector Subscription Framework for Magento 2
## TL;DR
This Magento 2 module adds a framework for adapting any subscription module to be used Customweb_CollectorCw module's CollectorDirect payment method.

## Functionalities
This module does not add any functionality for actual recurring orders and subscriptions.
You need to have another (custom, third party...) module for implementing that.

This module provides a framework for implementing an adapter between Customweb_CollectorCw module and the subscription module.
You can buy the Customweb_CollectorCw module here: https://www.sellxed.com/shop/en/chf/collector-zahlungs-extension-fur-magento.html

## Compatibility
If your subscription module meets following criteria, this module can be useful:
* New orders are created in a php cli process, such as Magento cron job
* New orders are created through a quote object

## Installation
```
$ cd {magento_base_dir}
$ composer config repositories.collector-subscription-framework vcs git@github.com:ambientiaoy/magento2-collector-subscription-framework.git
$ composer require ambientia/collector-subscription-framework
$ bin/magento setup:upgrade
```

## Implementing a new adapter module
* Install Customweb_CollectorCw module
* Configure the module to work as a normal payment method on the checkout
* Create a new M2 module e.g. `MyCompany_CollectorSubscription`
  * Add `<module name="Ambientia_CollectorSubscriptionFramework"/>` in the `etc/module.xml` file
  * Add `ambientia/collector-subscription-framework` as a requirement in the `composer.json` file 
  * Create an implementation class for `\Ambientia\CollectorSubscriptionFramework\Api\SubscriptionDataResolverInterface` and add a preference to `etc/di.xml` file
  * Call `Ambientia\CollectorSubscriptionFramework\Model\Service\CheckoutManagement\CliAdapter::authorize()` in your subscription handler to create a new invoice

### etc/module.xml
```xml
<?xml version="1.0" ?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:framework:Module/etc/module.xsd">
    <module name="MyCompany_CollectorSubscription" setup_version="1.0.0">
        <sequence>
            <module name="Ambientia_CollectorSubscriptionFramework"/>
            <module name="MyCompany_MySubscriptionModule"/>
        </sequence>
    </module>
</config>
```

### composer.json
```json
{
    "name": "my-company/collector-subscription",
    "description": "This module adds MyCompany_MySubscriptionModule functionalities to Customweb_CollectorCw module.",
    "require": {
        "magento/framework": "101.0.*",
        "ambientia/collector-subscription-framework": "^1.0"
    },
    "type": "magento2-module",
    "version": "1.0.0",
    "license": [
        "proprietary"
    ],
    "autoload": {
        "files": [
            "registration.php"
        ],
        "psr-4": {
            "MyCompany\\CollectorSubscription\\": ""
        }
    }
}
```

### etc/di.xml
```xml
<?xml version="1.0" ?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">
    <preference for="Ambientia\CollectorSubscriptionFramework\Api\SubscriptionDataResolverInterface"
                type="MyCompany\CollectorSubscription\Model\SubscriptionDataResolver"/>
</config>
```

### SubscriptionDataResolver implementation
Create a new file `app/code/MyCompany/CollectorSubscription/Model/SubscriptionDataResolver.php` based on the below code example
```php
<?php


namespace Ambientia\CollectorSubscription\Model;


use Ambientia\CollectorSubscriptionFramework\Api\SubscriptionDataResolverInterface;

class SubscriptionDataResolver implements SubscriptionDataResolverInterface
{
    /**
     * @return string|null
     */
    public function getSsn(): ?string
    {
        // TODO: get the saved social security number (SSN) value for the currently processed subscription
    }

    /**
     * @return bool
     */
    public function getConditionCheckbox(): bool
    {
        // TODO: get the saved value if the customer has approved the conditions for the currently processed subscription
    }

    /**
     * @return bool
     */
    public function isSubscription(): bool
    {
        // TODO: Implement the logic that checks the app state, if the process is currently handling a subscription.
    }
}
```

### Call the authorize inside your subscription handler class
```php
<?php

use Ambientia\CollectorSubscriptionFramework\Model\Service\CheckoutManagement\CliAdapter;

class MySubscriptionHandler
{
    /**
     * @var CliAdapter
     */
    private $cliAdapter;

    /**
     * CollectorDirectPaymentHandler constructor.
     * @param CliAdapter $cliAdapter
     */
    public function __construct(CliAdapter $cliAdapter) {
        $this->cliAdapter = $cliAdapter;
    }

    /**
     * Example how to authorize the Collector Direct payment when a new order
     * was created by a cron job or cli command.
     */ 
    public function mySubsctiontionHandler()
    {
        // TODO: The subscription module produces a new order that implements Magento\Sales\Api\Data\OrderInterface
        $this->cliAdapter->authorize($order);
    }
}
```
