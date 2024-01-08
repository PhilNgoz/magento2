<?php
/**
 * Class TransportInterfacePlugin
 *
 * PHP version 8.2
 *
 * @category Sparsh
 * @package  Sparsh_Smtp
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
namespace Sparsh\Smtp\Plugin\Mail;

use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\TransportInterface;
use Sparsh\Smtp\Helper\Data;
use Magento\Framework\Phrase;
use Laminas\Mail\Transport\Smtp;
use Laminas\Mail\Transport\SmtpOptions;
use Laminas\Mail\Message;
/**
 * Class TransportInterfacePlugin
 *
 * @category Sparsh
 * @package  Sparsh_Smtp
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class TransportInterfacePlugin
{

    /**
     * SparshSmtpHelper
     *
     * @var \Sparsh\Smtp\Helper\Data
     */
    private $smtpHelper;

    /**
     * Constructor
     *
     * @param Data $smtpHelper sparshSmtpHelper
     */
    public function __construct(
        Data $smtpHelper
    ) {
        $this->smtpHelper = $smtpHelper;
    }

    /**
     * Omit email sending or send mail via smtp depending on the system configuration setting
     *
     * @param TransportInterface $subject subject
     * @param \Closure           $proceed proceed
     *
     * @return void
     *
     * @throws MailException
     */
    public function aroundSendMessage(
        TransportInterface $subject,
        \Closure $proceed
    ) {

        if ($this->smtpHelper->getIsEmailEnabled()) {
            $message = $subject->getMessage();

            $isSetReturnPath = $this->smtpHelper->getIsSetReturnpath();
            $returnPathValue = $this->smtpHelper->getReturnpathValue();

            $smtpConf = [
                'host' => $this->smtpHelper->getSmtpHostConfig(),
                'port' => $this->smtpHelper->getSmtpPortConfig()
            ];

            if (version_compare($this->smtpHelper->getMagentoVersion(), '2.2.7', '>')) {
                $laminasMessage = Message::fromString($message->getRawMessage())->setEncoding('utf-8');
                if (2 === $isSetReturnPath && $returnPathValue) {
                    $laminasMessage->setSender($returnPathValue);
                } elseif (1 === $isSetReturnPath && $laminasMessage->getFrom()->count()) {
                    $fromAddressList = $laminasMessage->getFrom();
                    $fromAddressList->rewind();
                    $laminasMessage->setSender($fromAddressList->current()->getEmail());
                }

                $smtpConf = new SmtpOptions($smtpConf);
                $message = $laminasMessage;
                if (strtolower($this->smtpHelper->getSmtpAuthConfig()) != 'none') {
                    ($this->smtpHelper->getSmtpAuthConfig() == 'cram-md5') ? $auth = 'plain':$auth = $this->smtpHelper->getSmtpAuthConfig();
                    $smtpConf->setConnectionClass(strtolower($auth));
                    $connectionConfig = [
                        'username' => $this->smtpHelper->getSmtpUsername(),
                        'password' => $this->smtpHelper->getSmtpPassword()
                    ];
                }

                if ($this->smtpHelper->getSmtpSslConfig() != 'none') {
                    $connectionConfig['ssl'] = $this->smtpHelper->getSmtpSslConfig();
                }

                if (!empty($connectionConfig)) {
                    $smtpConf->setConnectionConfig($connectionConfig);
                }
                $transport = new Smtp();
                $transport->setOptions($smtpConf);
				
            } else {
                if (strtolower($this->smtpHelper->getSmtpAuthConfig() != 'none')) {
                    (strtolower($this->smtpHelper->getSmtpAuthConfig()) == 'cram-md5') ? $auth = 'plain' : $auth = strtolower($this->smtpHelper->getSmtpAuthConfig());
                    $smtpConf['connection_class'] = $auth;
                    $smtpConf['connection_config']['username'] = $this->smtpHelper->getSmtpUsername();
                    $smtpConf['connection_config']['password'] = $this->smtpHelper->getSmtpPassword();
                }

                if ($this->smtpHelper->getSmtpSslConfig() != 'none') {
                    $smtpConf['connection_config']['ssl'] = $this->smtpHelper->getSmtpSslConfig();
                }
                $transport = new Smtp();
                $transport->setOptions(new SmtpOptions($smtpConf));				
            }

            try {
                $transport->send($message);
            } catch (\Exception $e) {
                throw new MailException(new Phrase($e->getMessage()), $e);
            }
        } else {
            $proceed();
        }
    }
}
