# Mailing pro Nette Framework
Nastavení v **config.neon**
```neon
extensions:
    - NAttreid\Mailing\DI\MailingExtension

mailing:
    sender: 'sender <info@test.cz>'
    mailer: 
        - App/Mailer
        - App/Mailer2('sender2 <info2@test.cz>')
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
        $mail = $this->createMail('template');
        // nebo
        $mail = $this->createMail('<body><p>sablona jako string</p></body>);
        $mail->fromString();

        $mail->link = $this->link('someLink', [
            'hash' => $hash
        ]);

        $mail->setSubject($this->translate('translateMessage'))
                ->addTo($email);

        $mail->send();
    }
```

Odeslání
```php
/** @var \App\Services\Mailer @inject */
public $mailer;

$this->mailer->sendRestorePassword($email, $hash);
```