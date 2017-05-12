<?php

declare(strict_types=1);

namespace NAttreid\Mailing;

use Latte\Engine;
use Latte\Loaders\StringLoader;
use Nette\Application\LinkGenerator;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nette\Bridges\ApplicationLatte\UIMacros;
use Nette\Localization\ITranslator;
use Nette\Mail\IMailer;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;

/**
 * Class Mail
 *
 * @author Attreid <attreid@gmail.com>
 */
class Mail
{
	/** @var IMailer */
	private $mailer;

	/** @var ITranslator */
	private $translator;

	/** @var string */
	private $template;

	/** @var array */
	private $variables;

	/** @var Message */
	private $message;

	/** @var Engine */
	private $latte;

	/** @var string */
	private $basePath;

	/** @var string */
	private $imagePath;

	/** @var StringLoader */
	private $loader;

	public function __construct(string $template, string $basePath, ILatteFactory $latteFactory, LinkGenerator $linkGenerator, IMailer $mailer, ?ITranslator $translator)
	{
		$this->latte = $latteFactory->create();

		UIMacros::install($this->latte->getCompiler());

		$this->latte->addProvider('uiControl', $linkGenerator);
		$this->latte->addFilter(null, 'NAttreid\Latte\Filters::common');

		$this->basePath = $basePath . '/templates/';
		$this->imagePath = $this->basePath . 'images/';

		$this->template = $template;
		$this->message = new Message;
		$this->mailer = $mailer;

		$this->setTranslator($translator);
	}


	public function setTranslator(ITranslator $translator = null)
	{
		$this->latte->addFilter('translate', $translator === null ? null : [$translator, 'translate']);
		$this->translator = $translator;
	}

	/**
	 * Nastavi email na content z retezce
	 */
	public function setStringLoader(): void
	{
		$this->loader = new StringLoader;
	}

	public function setVariables(array $variables): void
	{
		$this->variables = $variables;
	}

	public function __set(string $name, $value)
	{
		$this->variables[$name] = $value;
	}

	public function __get(string $name)
	{
		return $this->variables[$name];
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
	 * Nastavi predmet
	 * @param mixed $subject
	 * @return self
	 */
	public function setSubject($subject): self
	{
		$this->message->setSubject($this->translator ? $this->translator->translate($subject) : (string) $subject);
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
	 * Prida prilohu
	 * @param string $file
	 * @param string|null $content
	 * @param string|null $contentType
	 * @return self
	 */
	public function addAttachment(string $file, string $content = null, string $contentType = null): self
	{
		$this->message->addAttachment($file, $content, $contentType);
		return $this;
	}

	/**
	 * Odesle mail
	 */
	public function send(): void
	{
		if ($this->loader !== null) {
			$this->latte->setLoader($this->loader);
			$body = $this->latte->renderToString($this->template, $this->variables);
		} else {
			$body = $this->latte->renderToString($this->basePath . $this->template . '.latte', $this->variables);
		}

		$this->message->setHtmlBody($body, $this->imagePath);
		$this->mailer->send($this->message);
	}
}

interface IMailFactory
{
	public function create(string $template, string $basePath): Mail;
}
