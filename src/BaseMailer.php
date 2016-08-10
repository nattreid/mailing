<?php

namespace NAttreid\Mailing;

use Nette\Application\LinkGenerator,
    Nette\Mail\IMailer,
    Nette\Localization\ITranslator;

/**
 * Mailer
 *
 * Attreid <attreid@gmail.com>
 */
abstract class BaseMailer {

    use \Nette\SmartObject;

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

    public function __construct($sender, $basePath, LinkGenerator $linkGenerator, Imailer $mailer, ITranslator $translator = NULL) {
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
    protected function createMail($template) {
        $mail = new Mail($template, $this->basePath, $this->linkGenerator, $this->mailer, $this->translator);
        $mail->setFrom($this->sender);
        return $mail;
    }

    /**
     * Generuje link
     * @param  string   destination in format "[[module:]presenter:]action" or "signal!" or "this"
     * @param  array|mixed
     * @return string
     * @throws InvalidLinkException
     */
    protected function link($destination, $args = []) {
        return $this->linkGenerator->link($destination, $args);
    }

    /**
     * Translates the given string.
     * @param  string   message
     * @param  int      plural count
     * @return string
     */
    protected function translate($message, $count = NULL) {
        return $this->translator !== NULL ? $this->translator->translate($message, $count) : $message;
    }

}
