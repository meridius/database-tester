# Database tester
Collection of classes intended to ease concurrent integration database testing with [Nette/Database](https://github.com/nette/database) and [Nette/Tester](https://github.com/nette/tester).

## Attribution
Huge **thank you** goes to Jiří Pudil for his article [Bootstrap your integration testing database](https://jiripudil.cz/blog/bootstrap-your-integration-testing-database) focusing on integration database testing with Doctrine.

## Howto
Here is sample integration database testcase. You just need to adjust paths to `bootstrap.php` and directory with SQL files.
```php
<?php

/**
 * @testCase
 */

use Meridius\DatabaseTester\DatabaseTestCase;
use Nette\Database\Connection;
use Nette\Database\Helpers;
use Nette\Utils\Finder;
use Service\Manager\UserManager;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

class DatabaseTest extends DatabaseTestCase {

	/** @var UserManager */
	private $userManager;

	public function __construct() {
		parent::__construct();
		$this->userManager = $this->getContainer()->getByType(UserManager::class);
	}

	protected function setupDatabaseContent(Connection $db) {
		$files = Finder::findFiles('*.sql')->in(__DIR__ . '/../files');
		foreach ($files as $file) {
			Helpers::loadFromFile($db, $file);
		}
	}

	public function testNonExistingId() {
		$row = $this->userManager->getById(0);
		Assert::null($row);
	}

}

(new DatabaseTest())->run();

```


And here is the mentioned `bootstrap.php` in which you also need to adjust paths.
```php
<?php

require __DIR__ . '/../../../../vendor/autoload.php';

if (!class_exists('Tester\Assert')) {
	echo 'Missing Nette Tester';
	exit(1);
}

Tester\Environment::setup();
date_default_timezone_set('Europe/Prague');

define( // each testCase has its own but container is shared
	'TEMP_DIR', 
	__DIR__ . '/../../../../temp/' . (
		isset($_SERVER['argv']) 
			? md5(serialize($_SERVER['argv'])) 
			: getmypid()
	)
);

define('TESTS_NEON', __DIR__ . '/tests.neon');

Tester\Helpers::purge(TEMP_DIR);

Tracy\Debugger::$logDirectory = TEMP_DIR;

```


The last thing remaining to do is to setup `tests.neon` with config changes just for the testing environment, if you don't have it already. Here is how it should look.
```neon
database:
	default:
		dsn: "mysql:host=127.0.0.1"
		user: root
		password: root

```