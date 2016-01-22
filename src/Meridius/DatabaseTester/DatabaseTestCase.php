<?php

namespace Meridius\DatabaseTester;

use Nette\Database\Connection;
use Tester\TestCase;

/**
 * Each test method will get its own database
 */
abstract class DatabaseTestCase extends TestCase {

	use DatabaseSetup;

	/**
	 * This will jump-start the whole process
	 */
	public function __construct() {
		$this->getContainer();
	}

	/**
	 * This is for populating the created database with testing data
	 * @param Connection $db
	 */
	abstract protected function setupDatabaseContent(Connection $db);

}
