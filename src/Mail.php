<?php

declare(strict_types=1);

namespace NAttreid\Mailing;

use Latte\Engine;
use Latte\Loaders\StringLoader;
use Nette\Application\LinkGenerator;
use Nette\Bridges\ApplicationLatte\UIMacros;
use Nette\Localization\ITranslator;
use Nette\Mail\IMailer;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;

class Mail
{
	/** @var IMailer */
	private $mailer;

	/** @var string */
	private $template;

	/** @var array */
	private $params;

	/** @var Message */
	private $message;

	/** @var Engine */
	private $latte;

	/** @var bool */
	private $fromString = false;

	/** @var string */
	private $basePath;

	/** @var string */
	private $imagePath;

	public function __construct(string $template, string $basePath, LinkGenerator $linkGenerator, IMailer $mailer, ?ITranslator $translator)
	{
		$this->latte = new Engine;

		UIMacros::install($this->latte->getCompiler());

		$this->latte->addFilter('translate', $translator === null ? null : [$translator, 'translate']);
		$this->latte->addProvider('uiControl', $linkGenerator);
		$this->latte->addFilter(null, 'NAttreid\Latte\Filters::common');

		$this->basePath = $basePath . '/templates/';
		$this->imagePath = $this->basePath . 'images/';

		$this->template = $template;
		$this->message = new Message;
		$this->mailer = $mailer;
	}

	/**
	 * Nastavi email na content z retezce
	 */
	public function fromString(): void
	{
		$this->fromString = true;
	}

	public function __set(string $name, $value)
	{
		$this->params[$name] = $value;
	}

	public function __get(string $name)
	{
		return $this->params[$name];
	}

	/**
	 * Nastave cestu k obrazkum
	 * @param string $path
	 * @return self
	 */
	public function setImagePath(string $path): self
	{
		$this->imagePath = $path;
		return $this;
	}

	/**
	 * Nastavi argumenty pro mailer
	 * @param string $args
	 * @return self
	 */
	public function setCommand(string $args): self
	{
		if ($this->mailer instanceof SendmailMailer) {
			$this->mailer->commandArgs = $args;
		}
		return $this;
	}

	/**
	 * Nastaveni returnPath pro SendmailMailer
	 * @param string $mail
	 * @return self
	 */
	public function setReturnPath(string $mail): self
	{
		$this->setCommand('-f' . $mail);
		return $this;
	}

	/**
	 * Nastavi Predmet
	 * @param string $subject
	 * @return self
	 */
	public function setSubject(string $subject): self
	{
		$this->message->setSubject($subject);
		return $this;
	}

	/**
	 * Prida prijemce
	 * @param string $email
	 * @param string $name
	 * @return self
	 */
	public function addTo(string $email, string $name = null): self
	{
		$this->message->addTo($email, $name);
		return $this;
	}

	/**
	 * Nastavi odesilatele
	 * @param string $email
	 * @param string $name
	 * @return self
	 */
	public function setFrom(string $email, string $name = null): self
	{
		$this->message->setFrom($email, $name);
		return $this;
	}

	/**
	 * Odesle mail
	 */
	public function send(): void
	{
		if ($this->fromString) {
			$this->latte->setLoader(new StringLoader);
			$body = $this->latte->renderToString($this->template, $this->params);
		} else {
			$body = $this->latte->renderToString($this->basePath . $this->template . '.latte', $this->params);
		}

		$this->message->setHtmlBody($body, $this->imagePath);
		$this->mailer->send($this->message);
	}

}
