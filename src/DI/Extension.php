<?php

namespace NAttreid\Mailing\DI;

/**
 * Rozsireni
 *
 * Attreid <attreid@gmail.com>
 */
class Extension extends \Nette\DI\CompilerExtension {

    /** @var array */
    private $defaults = [
        'path' => '',
        'class' => '',
        'sender' => '',
    ];

    public function loadConfiguration() {
        $config = $this->validateConfig($this->defaults, $this->config);

        $builder = $this->getContainerBuilder();

        if (!isset($config['path'])) {
            throw new \Nette\InvalidArgumentException("Missing value 'path' for mailing");
        } elseif (!isset($config['class'])) {
            throw new \Nette\InvalidArgumentException("Missing value 'class' for mailing");
        }

        $builder->addDefinition($this->prefix('mailing'))
                ->setClass($config['class']);

        $builder->addDefinition($this->prefix('mailing.mail'))
                ->setImplement('\NAttreid\Mailing\IMail')
                ->setFactory('\NAttreid\Mailing\Mail')
                ->setArguments(['%template%', $config['path']])
                ->addSetup('setFrom', [$config['sender']]);
    }

}
