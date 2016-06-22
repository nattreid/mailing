# Mailing pro Nette Framework
Nastavení v **config.neon**
```neon
extensions:
    - NAttreid\Mailing\DI\Extension

mailing:
    sender: 'sender <info@test.cz>'
    path: %appDir%/templates/mailing/
    class: App/Service/Mailing
```

## Použití
Vytvořte třídu děděním z **\NAttreid\Mailing\BaseMailer**
```php
class Mailer extends \NAttreid\Mailing\BaseMailer {

    /**
     * Odeslani linku pro zmenu hesla
     * @param string $email
     * @param string $hash
     */
    public function sendRestorePassword($email, $hash) {
        $mail = $this->mailFactory->create('template');
        // nebo
        $mail = $this->mailFactory->create('<body><p>sablona jako string</p></body>);
        $mail->fromString();

        $mail->link = $this->link('someLink', [
            'hash' => $hash
        ]);

        $mail->setSubject($this->translate('translateMessage'))
                ->addTo($email);

        $mail->send();
    }
```