<?php

declare(strict_types=1);

namespace NAttreid\Mailing;

use Nette\Application\LinkGenerator;
use Nette\Application\UI\InvalidLinkException;
use Nette\Localization\ITranslator;
use Nette\Mail\IMailer;
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

	/** @var string[] */
	private $variables;

	/** @var string */
	private $basePath;

	/** @var IMailer */
	private $mailer;

	/** @var LinkGenerator */
	private $linkGenerator;

	/** @var ITranslator */
	private $translator;

	public function __construct(string $sender, array $variables, string $basePath, LinkGenerator $linkGenerator, IMailer $mailer, ?ITranslator $translator)
	{
		$this->sender = $sender;
		$this->variables = $variables;
		$this->basePath = $basePath;
		$this->mailer = $mailer;
		$this->linkGenerator = $linkGenerator;
		$this->translator = $translator;
	}

	/**
	 * Vytvori instanci mailu
	 * @param string $template
	 * @return Mail
	 */
	protected function createMail(string $template): Mail
	{
		$mail = new Mail($template, $this->basePath, $this->linkGenerator, $this->mailer, $this->translator);
		$mail->setFrom($this->sender);
		foreach ($this->variables as $variable => $value) {
			$mail->{$variable} = $value;
		}
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

	/**
	 * Translates the given string.
	 * @param  string $message message
	 * @param  int $count plural count
	 * @return string
	 */
	protected function translate(string $message, int $count = null): string
	{
		return $this->translator !== null ? $this->translator->translate($message, $count) : $message;
	}

	protected function getVariable(string $name):?string
	{
		return $this->variables[$name]?? null;
	}

}
