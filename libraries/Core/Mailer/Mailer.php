<?php 
namespace LegoAsync\Mailer;
class Mailer
{
    protected $settings;
    protected $provider = null;
    const MODE_SMTP = 'smtp';
    const MODE_DEFAULT = 'mail';
    public function __construct($settings = [])
    {
        $this->settings = $settings;
        $config = $settings['config'];
        try{
            if($settings['type'] == self::MODE_SMTP)
            {
                $transport = (new \Swift_SmtpTransport($config['host'], $config['port'],$config['security']))
                ->setUsername($config['user'])
                ->setPassword($config['password']);
            }
            else
            {
                $transport = new \Swift_SendmailTransport('/usr/sbin/sendmail -bs');
            }
            $this->provider = new \Swift_Mailer($transport);
        }
        catch(\Exception $ex)
        {
            
        }
        
    }
    /**
     * Return swift mailer instance
     * @return \Swift_Mailer
     */
    public function getProvider()
    {
        return $this->provider;
    }
    /**
     *
     * @param string $title
     * @param string $body
     * @param array $from. Example ['john@doe.com' => 'John Doe']
     * @param array $to. Example ['receiver@domain.org', 'other@domain.org' => 'A name']
     * @param true|false $forceHTML. Force message sending using html encode
     */
    public function sendMessage($title, $body, $to , $from = [], $cc = [], $bcc = [], $forceHTML = true)
    {
        $provider = $this->getProvider();
        if($provider)
        {
            if(empty($to))
            {
                throw new \RuntimeException("Required receiption");
            }
            if(is_string($to))
            {
                $to = [$to];
            }
            
            if(!is_array($from) || !count($from))
            {
                $from = $this->settings['from'];
            }
            $message = (new \Swift_Message($title))
            ->setFrom($from)
            ->setTo($to)
            ->setBody($body)
            ;
            if($forceHTML)
            {
                $message->setContentType('text/html');
            }
            if(is_array($cc) && count($cc))
            {
                $message->setCC($cc);
            }
            if(is_array($bcc) && count($bcc))
            {
                $message->setBcc($bcc);
            }
            // Send the message
            return $provider->send($message);
            
        }
        return false;
    }
    public function sendToQueue()
    {
        
    }
}