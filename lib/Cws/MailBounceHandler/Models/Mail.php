<?php

/**
 * Mail.
 *
 * @author Cr@zy
 * @copyright 2013-2015, Cr@zy
 * @license GNU LESSER GENERAL PUBLIC LICENSE
 *
 * @link https://github.com/crazy-max/CwsMailBounceHandler
 */
namespace Cws\MailBounceHandler\Models;

class Mail
{
    /**
     * Message number or filename.
     *
     * @var int|string
     */
    private $token;

    /**
     * Was processed during bounce or fbl analyze.
     *
     * @var bool
     */
    private $processed;

    /**
     * Message subject.
     *
     * @var string
     */
    private $subject;

    /**
     * Type detected (bounce or fbl).
     *
     * @var string
     */
    private $type;

    /**
     * List of recipients,.
     *
     * @see Cws\MailBounceHandler\Models\Recipient object
     *
     * @var Recipient[]
     */
    private $recipients;

	/** @var  object */
	protected $headers = null;

	/** @var  string */
	protected $body = null;

	protected $imapResource = null;

	static function createLazyObject($imapResource) {
		$instance = new self;
		$instance->setLazyAttributes($imapResource);
		return $instance;
	}

    public function __construct()
    {
        $this->token = null;
        $this->processed = true;
        $this->subject = null;
        $this->type = null;
        $this->recipients = [];
    }

    private function setLazyAttributes($imapResource) {
		$this->imapResource = $imapResource;
	}

    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function isProcessed()
    {
        return $this->processed;
    }

    public function setProcessed($processed)
    {
        $this->processed = $processed;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getRecipients()
    {
        return $this->recipients;
    }

    public function addRecipient(Recipient $recipient)
    {
        $this->recipients[] = $recipient;
    }

	function __sleep()
	{
		$this->getBody();
		$this->imapResource = null;
	}

	public function getBody() {
		if(!$this->body) {
			$this->body = imap_body($this->imapResource, $this->token, FT_UID);
		}
		return $this->body;
	}

	public function getHeaders() {
		return imap_rfc822_parse_headers($this->getBody());
	}

}
