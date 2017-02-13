<?php

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
	private $imagePath = 'images/';

	public function __construct($template, $basePath, LinkGenerator $linkGenerator, IMailer $mailer, ITranslator $translator = null)
	{
		$this->latte = new Engine;

		UIMacros::install($this->latte->getCompiler());

		$this->latte->addFilter('translate', $translator === null ? null : [$translator, 'translate']);
		$this->latte->addProvider('uiControl', $linkGenerator);
		$this->latte->addFilter(null, 'NAttreid\Latte\Filters::common');

		$this->basePath = $basePath . '/templates/';
		$this->template = $template;
		$this->message = new Message;
		$this->mailer = $mailer;
	}

	/**
	 * Nastavi email na content z retezce
	 */
	public function fromString()
	{
		$this->fromString = true;
	}

	public function __set($name, $value)
	{
		$this->params[$name] = $value;
	}

	public function __get($name)
	{
		return $this->params[$name];
	}

	/**
	 * Nastave cestu k obrazkum
	 * @param string $path
	 */
	public function setImagePath($path)
	{
		$this->imagePath = $path;
	}

	/**
	 * Nastavi argumenty pro mailer
	 * @param string $args
	 */
	public function setCommand($args)
	{
		if ($this->mailer instanceof SendmailMailer) {
			$this->mailer->commandArgs = $args;
		}
	}

	/**
	 * Nastaveni returnPath pro SendmailMailer
	 * @param string $mail
	 */
	public function setReturnPath($mail)
	{
		$this->setCommand('-f' . $mail);
	}

	/**
	 * Nastavi Predmet
	 * @param string $subject
	 * @return Message
	 */
	public function setSubject($subject)
	{
		return $this->message->setSubject($subject);
	}

	/**
	 * Prida prijemce
	 * @param string $email
	 * @param string $name
	 * @return Message
	 */
	public function addTo($email, $name = null)
	{
		return $this->message->addTo($email, $name);
	}

	/**
	 * Nastavi odesilatele
	 * @param string $email
	 * @param string $name
	 * @return Message
	 */
	public function setFrom($email, $name = null)
	{
		if (!empty($email)) {
			$this->message->setFrom($email, $name);
		}
		return $this->message;
	}

	/**
	 * Odesle mail
	 */
	public function send()
	{
		if ($this->fromString) {
			$this->latte->setLoader(new StringLoader);
			$body = $this->latte->renderToString($this->template, $this->params);
		} else {
			$body = $this->latte->renderToString($this->basePath . '/' . $this->template . '.latte', $this->params);
		}

		$this->message->setHtmlBody($body, $this->basePath . '/' . $this->imagePath);
		$this->mailer->send($this->message);
	}

}
