<?php

namespace NAttreid\Mailing;

use Nette\Application\LinkGenerator,
    Nette\Mail\IMailer,
    Kdyby\Translation\Translator;

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

    /** @var Translator */
    private $translator;

    /** @var IMail */
    protected $mailFactory;

    public function __construct(LinkGenerator $linkGenerator, Translator $translator, Imailer $mailer, IMail $mailFactory) {
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
     *
     * @param string  $message    The message id
     * @param integer $count      The number to use to find the indice of the message
     * @param array   $parameters An array of parameters for the message
     * @param string  $domain     The domain for the message
     * @param string  $locale     The locale
     *
     * @return string
     */
    protected function translate($message, $count = NULL, $parameters = [], $domain = NULL, $locale = NULL) {
        return $this->translator->translate($message, $count, $parameters, $domain, $locale);
    }

}
