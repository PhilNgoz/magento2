<?php
/**
 * Class TestMail
 *
 * PHP version 8.2
 *
 * @category Sparsh
 * @package  Sparsh_Smtp
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
namespace Sparsh\Smtp\Controller\Adminhtml\Smtp;

use Laminas\Mail\Transport\Smtp;
use Laminas\Mail\Transport\SmtpOptions;
use Laminas\Mail\Message;
use Laminas\Mime\Message as MessageBody;
use Laminas\Mime\Part;
use Laminas\Mime\Mime;

/**
 * Class TestMail
 *
 * @category Sparsh
 * @package  Sparsh_Smtp
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class TestMail extends \Magento\Backend\App\Action
{
    /**
     * EtatavasoftSmtpHelper
     *
     * @var \Sparsh\Smtp\Helper\Data
     */
    protected $smtpHelper;

    /**
     * JsonHelper
     *
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context    context
     * @param \Sparsh\Smtp\Helper\Data        $smtpHelper smtpHelper
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper jsonHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Sparsh\Smtp\Helper\Data $smtpHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    ) {
        $this->smtpHelper = $smtpHelper;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context);
    }

    /**
     * TestMail action
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $postParams = $this->getRequest()->getParams();
        $host = $this->smtpHelper->getSmtpHostConfig();
        $port = $this->smtpHelper->getSmtpPortConfig();
        $smtpConf  = [
            'name' => 'localhost',
            'host' => $host,
            'port' => $port
        ];
        $auth = strtolower($this->smtpHelper->getSmtpAuthConfig());
        $username = $this->smtpHelper->getSmtpUsername();
        $password = $this->smtpHelper->getSmtpPassword();

        if ($auth && $auth !== 'none') {
            ($auth == 'cram-md5') ? $auth = 'plain':'';
            $smtpConf['connection_class'] = $auth;
            $smtpConf['connection_config']['username'] = $username;
            $smtpConf['connection_config']['password'] = $password;
        }

        $ssl = $this->smtpHelper->getSmtpSslConfig();

        if ($ssl && $ssl !== 'none') {
            $smtpConf['connection_config']['ssl'] = $ssl;
        }

        $toEmail = $postParams['toEmail'];
        $fromEmail = $postParams['fromEmail'] ? $postParams['fromEmail'] : $username;

        if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL) || (!filter_var($postParams['fromEmail'], FILTER_VALIDATE_EMAIL) && !empty($postParams['fromEmail']))
        ) {
            $result = [
                'success' => 0,
                'message' => 'Please enter valid email address.'
            ];
            return $this->getResponse()->representJson(
                $this->jsonHelper->jsonEncode($result)
            );
        } else {
            $transport = new Smtp();
            $transport->setOptions(new SmtpOptions($smtpConf));
            $message = new Message();
            $content = "<p>Hello,</p><p><strong>SMTP configurations are done properly.</strong></p><p>Your store has been successfully connected and ready to send emails with SMTP.</p><p>Thank you for using Sparsh SMTP extension.</p><p><i>This is an e-mail message sent by Sparsh SMTP extension while testing the settings for your extension.</i></p>";
            $part = new Part($content);
            $part->setType(Mime::TYPE_HTML);
            $messageBody = new MessageBody();
            $messageBody->addPart($part);
            $message->setFrom($fromEmail);
            $message->addTo($toEmail);
            $message->setSubject(__('TEST EMAIL from Sparsh SMTP Extension'));
            $message->setBody($messageBody);
            try {
                $transport->send($message);
                $result = [
                    'success' => 1,
                    'message' => 'Mail sent successfully on ' . $toEmail . '. Please check your Mailbox.'
                ];
                return $this->getResponse()->representJson(
                    $this->jsonHelper->jsonEncode($result)
                );
            } catch (\Exception $e) {
                $result = [
                    'success' => 0,
                    'message' => $e->getMessage()
                ];
                return $this->getResponse()->representJson(
                    $this->jsonHelper->jsonEncode($result)
                );
            }
        }
    }
}
