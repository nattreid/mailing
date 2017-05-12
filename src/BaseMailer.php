<?php

declare(strict_types=1);

namespace NAttreid\Mailing;

use Nette\Application\LinkGenerator;
use Nette\Application\UI\InvalidLinkException;
use Nette\SmartObject;

/**
 * Class BaseMailer
 *
 * @author Attreid <attreid@gmail.com>
 */
abstract class BaseMailer
{

	use SmartObject;

	/** @var string */
	private $sender;

	/** @var string */
	private $basePath;

	/** @var IMailFactory */
	private $mailFactory;

	/** @var LinkGenerator */
	private $linkGenerator;

	public function __construct(string $sender, string $basePath, IMailFactory $mailFactory, LinkGenerator $linkGenerator)
	{
		$this->sender = $sender;
		$this->basePath = $basePath;
		$this->mailFactory = $mailFactory;
		$this->linkGenerator = $linkGenerator;
	}

	/**
	 * Vytvori instanci mailu
	 * @param string $template
	 * @return Mail
	 */
	protected function createMail(string $template): Mail
	{
		$mail = $this->mailFactory->create($template, $this->basePath);
		$mail->setFrom($this->sender);
		return $mail;
	}

	/**
	 * Vytvori instanci mailu
	 * @param string $latte
	 * @return Mail
	 */
	protected function createMailFromString(string $latte): Mail
	{
		$mail = $this->createMail($latte);
		$mail->setStringLoader();
		return $mail;
	}

	/**
	 * Generuje link
	 * @param string $destination destination in format "[[module:]presenter:]action" or "signal!" or "this"
	 * @param array $args
	 * @return string
	 * @throws InvalidLinkException
	 */
	protected function link(string $destination, array $args = []): string
	{
		return $this->linkGenerator->link($destination, $args);
	}

	protected function getVariable(string $name): ?string
	{
		return $this->variables[$name] ?? null;
	}
}
