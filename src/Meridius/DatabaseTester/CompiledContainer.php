<?php

namespace Meridius\DatabaseTester;

use Nette\Configurator;
use Nette\DI\Container;
use const TEMP_DIR;
use const TESTS_NEON;

trait CompiledContainer {

	/** @var Container */
	private $container;

	/**
	 *
	 * @return Container
	 */
	protected function getContainer() {
		if ($this->container === NULL) {
			$this->container = $this->createContainer();
		}

		return $this->container;
	}

	/**
	 *
	 * @return Container
	 */
	protected function createContainer() {
		// /vendor/meridius/database-tester/src/Meridius/DatabaseTester/
		$appDir = __DIR__ . '/../../../../../../app';

		$configurator = new Configurator();
		$configurator->setTempDirectory(dirname(TEMP_DIR)); // shared container for performance purposes

		$configurator->createRobotLoader()
			->addDirectory($appDir)
			->register();

		$configurator
			->setDebugMode(FALSE)
			->addParameters(['appDir' => $appDir]);

		$configurator
			->addConfig("$appDir/config/config.neon")
			->addConfig(TESTS_NEON);

		return $configurator->createContainer();
	}

}
