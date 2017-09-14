<?php

declare(strict_types=1);

namespace NAttreid\Mailing\DI;

use NAttreid\Mailing\IMailFactory;
use NAttreid\Mailing\Mail;
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
		'variables' => []
	];

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults, $this->config);

		$builder->addDefinition($this->prefix('mailFactory'))
			->setImplement(IMailFactory::class)
			->setFactory(Mail::class)
			->addSetup('setVariables', [$config['variables']]);

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
				->setType($class)
				->setArguments([$sender, $dir]);
		}
	}

}
