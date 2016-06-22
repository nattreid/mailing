<?php

namespace NAttreid\Mailing;

use Nette\Mail\Message,
    Nette\Application\LinkGenerator,
    Nette\Mail\IMailer,
    Kdyby\Translation\Translator;

class Mail {

    /** @var IMailer */
    private $mailer;
    private $template;
    private $params;
    private $message;
    private $latte;
    private $fromString = FALSE;
    private $basePath;
    private $imagePath = 'images/';

    public function __construct($template, $basePath, LinkGenerator $linkGenerator, Translator $translator, IMailer $mailer) {
        $this->latte = new \Latte\Engine;

        \Nette\Bridges\ApplicationLatte\UIMacros::install($this->latte->getCompiler());

        $this->latte->addFilter('translate', $translator === NULL ? NULL : [$translator, 'translate']);
        $this->latte->addFilter(NULL, '\NAttreid\Latte::common');

        $this->params = [
            '_control' => $linkGenerator // kvůli makru {link}
        ];

        $this->basePath = $basePath;
        $this->template = $template;
        $this->message = new Message;
        $this->mailer = $mailer;
    }

    /**
     * Nastavi email na content z retezce
     */
    public function fromString() {
        $this->fromString = TRUE;
    }

    public function __set($name, $value) {
        $this->params[$name] = $value;
    }

    public function __get($name) {
        return $this->params[$name];
    }

    /**
     * Nastave cestu k obrazkum
     * @param type $path
     */
    public function setImagePath($path) {
        $this->imagePath = $path;
    }

    /**
     * Nastavi argumenty pro mailer
     * @param string $args
     */
    public function setCommand($args) {
        if ($this->mailer instanceof \Nette\Mail\SendmailMailer) {
            $this->mailer->commandArgs = $args;
        }
    }

    /**
     * Nastaveni returnPath pro SendmailMailer
     * @param string $mail
     */
    public function setReturnPath($mail) {
        $this->setCommand('-f' . $mail);
    }

    /**
     * Nastavi Predmet
     * @param string $subject
     * @return Message
     */
    public function setSubject($subject) {
        return $this->message->setSubject($subject);
    }

    /**
     * Prida prijemce
     * @param string $email
     * @param string $name
     * @return Message
     */
    public function addTo($email, $name = NULL) {
        return $this->message->addTo($email, $name);
    }

    /**
     * Nastavi odesilatele
     * @param string $email
     * @param string $name
     * @return Message
     */
    public function setFrom($email, $name = NULL) {
        if (!empty($email)) {
            return $this->message->setFrom($email, $name);
        }
    }

    /**
     * Odesle mail
     */
    public function send() {
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

interface IMail {

    /**
     * @param string $template
     * @return Mail 
     */
    public function create($template);
}
