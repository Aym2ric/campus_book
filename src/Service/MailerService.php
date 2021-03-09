<?php


namespace App\Service;


use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * MailerService constructor.
     * @param MailerInterface $mailer
     */
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param string $to
     * @param string $subject
     * @param string $text
     * @param string $html
     * @throws TransportExceptionInterface
     */
    public function send(
        string $to,
        string $subject,
        string $html
    )
    {
        $email = (new Email())
            ->from('campus.academy.book@gmail.com')
            ->to($to)
            ->replyTo('campus.academy.book@gmail.com')
            ->subject($subject)
            ->html($html);
        try {
            $this->mailer->send($email);
        } catch (\Exception $exception) {

        }
    }

    /**
     * @param string $to
     * @param string $subject
     * @return void
     * @throws TransportExceptionInterface
     */
    public function demandeInscription(string $to, string $subject): void
    {
        $html = "<p>Votre demande d'inscription pour le compte " . $to . " a bien été validé. Vous receverez un mail de confirmation lorsque votre compte sera validé par l'équipe de CAMPUS BOOK.</p>";

        $this->send($to, $subject, $html);
    }

    /**
     * @param string $to
     * @param string $subject
     * @return void
     * @throws TransportExceptionInterface
     */
    public function activationCompte(string $to, string $subject): void
    {
        $html = "<p>Votre compte " . $to . " a bien été validé. Vous pouvez désormais vous connectez sur CAMPUS BOOK.</p>";

        $this->send($to, $subject, $html);
    }
}