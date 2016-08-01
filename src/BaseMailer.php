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

    /** @var IMailer */
    private $mailer;

    /** @var LinkGenerator */
    private $linkGenerator;

    /** @var ITranslator */
    private $translator;

    /** @var IMail */
    protected $mailFactory;

    public function __construct(LinkGenerator $linkGenerator, Imailer $mailer, IMail $mailFactory, ITranslator $translator = NULL) {
        $this->mailer = $mailer;
        $this->linkGenerator = $linkGenerator;
        $this->translator = $translator;
        $this->mailFactory = $mailFactory;
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
