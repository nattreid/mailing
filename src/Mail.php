<?php

namespace NAttreid\Mailing;

use Nette\Application\LinkGenerator;
use Nette\Localization\ITranslator;
use Nette\Mail\IMailer;
use Nette\Mail\Message;

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
	/** @var \Latte\Engine */
	private $latte;
	/** @var bool */
	private $fromString = FALSE;
	/** @var string */
	private $basePath;
	/** @var string */
	private $imagePath = 'images/';

	public function __construct($template, $basePath, LinkGenerator $linkGenerator, IMailer $mailer, ITranslator $translator = NULL)
	{
		$this->latte = new \Latte\Engine;

		\Nette\Bridges\ApplicationLatte\UIMacros::install($this->latte->getCompiler());

		$this->latte->addFilter('translate', $translator === NULL ? NULL : [$translator, 'translate']);
		$this->latte->addProvider('uiControl', $linkGenerator);
		$this->latte->addFilter(NULL, 'NAttreid\Latte::common');

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
		$this->fromString = TRUE;
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
		if ($this->mailer instanceof \Nette\Mail\SendmailMailer) {
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
	public function addTo($email, $name = NULL)
	{
		return $this->message->addTo($email, $name);
	}

	/**
	 * Nastavi odesilatele
	 * @param string $email
	 * @param string $name
	 * @return Message
	 */
	public function setFrom($email, $name = NULL)
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
			$this->latte->setLoader(new \Latte\Loaders\StringLoader);
			$body = $this->latte->renderToString($this->template, $this->params);
		} else {
			$body = $this->latte->renderToString($this->basePath . '/' . $this->template . '.latte', $this->params);
		}

		$this->message->setHtmlBody($body, $this->basePath . '/' . $this->imagePath);
		$this->mailer->send($this->message);
	}

}
