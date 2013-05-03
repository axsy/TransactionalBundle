AxsyTransactionalBundle
=======================

This bundle provides transactional wrapper for the controllers and services. In the most simple case the usage is as simple
as adding the @Transactionable annotation to the Controller action:

```php
use Axsy\TransactionalBundle\Annotation\Transactionable;

// Acme/SomeBundle/Controllers/SomeController.php
class SomeController extends Controller
{
    /**
     * @Transactionable
     */
    public function performRollbackOnExceptionAction()
    {
        // Persist some changes to the database using Doctrine DBAL or Doctrine ORM, whatever
        // ...
        // ...

        // Throw an exceptions
        // All changes performed upper will be rolled back
        throw new \RuntimeException();
    }
}
```

or to some service method:

```php
use Axsy\TransactionalBundle\Annotation\Transactionable;

// Acme/SomeBundle/SomeService.php
class SomeService
{
    /**
     * @Transactionable
     */
    public function performRollbackOnException()
    {
        // ...
    }

    // ...
    // ...
}
```

There are some options to customize the @Transactionable behavior, see below.

Installation
------------

This bundle can be installed via `composer`. Just add the following lines to the `comsposer.json`

```js
// composer.json
{
    // ...
    require: {
        // ...
        "axsy/transactional-bundle": "dev-master"
    }
}
```

Please replace `dev-master` in the snippet above with the latest stable branch, for example ``1.0.*``.
Please check the tags on Github for which versions are available.

Then, you can install the new dependencies by running Composer's ``update`` command from the directory where your
``composer.json`` file is located:

```bash
php composer.phar update
```

Now, Composer will automatically download all required files, and install them
for you. All that is left to do is to update your ``AppKernel.php`` file, and
register the new bundle:

```php
// in AppKernel::registerBundles()
$bundles = array(
    // ...
    new Axsy\TransactionalBundle\AxsyTransactionalBundle(),
    // ...
);
```

Please make sure, that `JMS\AopBundle\JMSAopBundle` and `JMS\DiExtraBundle\JMSDiExtraBundle` registred too. They're
already registered in the Symfony Standard Edition distribution out of the box.

Configuration
-------------

This bundle allows to set the default Doctrine DBAL connection to be used and\or default transaction isolation level.
By defaut the following settings are accepted:

```yaml
axsy_transactional:
    default_connection:   default

    # Supported isolations are read_uncommitted, read_committed, repeatable_read, serializable
    default_isolation:    read_committed
```

@Transactionable
----------------

This annotation allows to override the default connection name and transaction isolation level:

```php

use Axsy\TransactionalBundle\Annotation\Transactionable;

// Acme/SomeBundle/SomeService.php
class SomeService
{
    /**
     * Transactionable(connection="other", isolation="read_uncommitted")
     */
    public function performRollbackOnException()
    {
        // ...
    }
}
```

Also you can explicitly enumerate the class names of the exceptions that will be 'transparent' for the transaction and it
will be committed successfully:

```php
use Axsy\TransactionalBundle\Annotation\Transactionable;

// Acme/SomeBundle/Controllers/SomeController.php
class SomeController extends Controller
{
    /**
     * @Transactionable(noRollbackFor={"Symfony\Component\HttpKernel\Exception\NotFoundHttpException"})
     */
    public function performCommitOnNotFoundHttpExceptionAction()
    {
        // ...
    }
}
```

and vice versa:

```php
use Axsy\TransactionalBundle\Annotation\Transactionable;

// Acme/SomeBundle/SomeService.php
class SomeService
{
    /**
     * @Transactionable(rollbackFor={"Acme\SomeBundle\Exceptions\VeryBadException"})
     */
    public function performRollbackOnVeryBadExceptionOnly()
    {
        // ...
    }
}
```

@Transactionable annotation can be defined on the class level. This way all methods of the controller/service will be
annotated silently too:

```php
use Axsy\TransactionalBundle\Annotation\Transactionable;

// Acme/SomeBundle/Controllers/SomeController.php
/**
 * @Transactionable(noRollbackFor={"Symfony\Component\HttpKernel\Exception\NotFoundHttpException"})
 */
class SomeController extends Controller
{
    public function performCommitOnNotFoundHttpExceptionAction()
    {
        // ...
    }

    public function thisTooAction()
    {
        // ...
    }
}
```

It is possible to override some settings of globally defined annotation on the method level:

```php
use Axsy\TransactionalBundle\Annotation\Transactionable;

// Acme/SomeBundle/SomeService.php
/**
 * @Transactionable(rollbackFor={"Acme\SomeBundle\Exceptions\VeryBadException"})
 */
class SomeService
{
    public function performRollbackOnVeryBadExceptionOnlyOnDefaultConnection()
    {
        // ...
    }

    /**
     * @Transactionable(connection="only")
     */
    public function performRollbackOnVeryBadExceptionOnlyOnOtherConnection()
    {
        // ...
    }
}
```

Tests
-----

You can simply run the tests for the bundle, run the following commands from the root of the bundle:

```bash
cp phpunit.xml.desc phpunit.xml
phpunit -c phpunit.xml
```