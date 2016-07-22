<?php

namespace NAttreid\Mailing\DI;

use NAttreid\Mailing\IMail,
    NAttreid\Mailing\Mail;

/**
 * Rozsireni
 *
 * Attreid <attreid@gmail.com>
 */
class MailingExtension extends \Nette\DI\CompilerExtension {

    /** @var array */
    private $defaults = [
        'path' => NULL,
        'class' => NULL,
        'sender' => '',
    ];

    public function loadConfiguration() {
        $config = $this->validateConfig($this->defaults, $this->config);

        $builder = $this->getContainerBuilder();

        if (!isset($config['class'])) {
            throw new \Nette\InvalidArgumentException("Missing value 'class' for mailing");
        }

        $builder->addDefinition($this->prefix('mailing'))
                ->setClass($config['class']);

        $builder->addDefinition($this->prefix('mailing.mail'))
                ->setImplement(IMail::class)
                ->setFactory(Mail::class)
                ->setArguments(['%template%', $config['path']])
                ->addSetup('setFrom', [$config['sender']]);
    }

}
