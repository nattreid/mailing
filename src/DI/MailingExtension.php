<?php

namespace NAttreid\Mailing\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Statement;
use Nette\Reflection\ClassType;

/**
 * Rozsireni
 *
 * Attreid <attreid@gmail.com>
 */
class MailingExtension extends CompilerExtension
{

	/** @var array */
	private $defaults = [
		'mailer' => [],
		'sender' => null,
	];

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults, $this->config);

		$counter = 1;
		foreach ($config['mailer'] as $mailer) {
			$sender = $config['sender'];
			if ($mailer instanceof Statement) {
				$class = $mailer->getEntity();
				if (isset($mailer->arguments[0])) {
					$sender = $mailer->arguments[0];
				}
			} else {
				$class = $mailer;
			}

			$rc = new ClassType($mailer);
			$dir = dirname($rc->getFileName());
			$builder->addDefinition($this->prefix('mailer.' . $counter++))
				->setClass($class)
				->setArguments([$sender, $dir]);
		}
	}

}
