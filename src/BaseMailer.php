<?php

namespace NAttreid\Mailing;

use Nette\Application\LinkGenerator;
use Nette\Application\UI\InvalidLinkException;
use Nette\Localization\ITranslator;
use Nette\Mail\IMailer;
use Nette\SmartObject;

/**
 * Mailer
 *
 * Attreid <attreid@gmail.com>
 */
abstract class BaseMailer
{

	use SmartObject;

	/** @var string */
	private $sender;

	/** @var string */
	private $basePath;

	/** @var IMailer */
	private $mailer;

	/** @var LinkGenerator */
	private $linkGenerator;

	/** @var ITranslator */
	private $translator;

	public function __construct($sender, $basePath, LinkGenerator $linkGenerator, IMailer $mailer, ITranslator $translator = null)
	{
		$this->sender = $sender;
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
	protected function createMail($template)
	{
		$mail = new Mail($template, $this->basePath, $this->linkGenerator, $this->mailer, $this->translator);
		$mail->setFrom($this->sender);
		return $mail;
	}

	/**
	 * Generuje link
	 * @param $destination string destination in format "[[module:]presenter:]action" or "signal!" or "this"
	 * @param $args array|mixed
	 * @return string
	 * @throws InvalidLinkException
	 */
	protected function link($destination, $args = [])
	{
		return $this->linkGenerator->link($destination, $args);
	}

	/**
	 * Translates the given string.
	 * @param  string $message message
	 * @param  int $count plural count
	 * @return string
	 */
	protected function translate($message, $count = null)
	{
		return $this->translator !== null ? $this->translator->translate($message, $count) : $message;
	}

}
