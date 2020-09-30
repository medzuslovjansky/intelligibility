<?php

declare(strict_types=1);

namespace Intelligibility\CoreBundle\Util\Translation\Extractor;

use Intelligibility\CoreBundle\Util\Translation\Extractor\Traits\IsRunTrait;
use Symfony\Component\Translation\Extractor\ExtractorInterface;
use Symfony\Component\Translation\MessageCatalogue;

class ListExtractor implements ExtractorInterface
{
    use IsRunTrait;
    /**
     * @var string
     */
    protected $prefix;
    protected array $blackList;
    protected array $whiteList;

    public function __construct(array $translationList)
    {
        $this->whiteList = $translationList['whitelist'] ?? [];
        $this->blackList = $translationList['blackList'] ?? [];
    }

    /**
     * {@inheritdoc}
     */
    public function setPrefix($prefix): void
    {
        $this->prefix = $prefix;
    }

    /**
     * {@inheritdoc}
     */
    public function extract($directory, MessageCatalogue $catalogue): void
    {
        if ($this->isRunAlready($directory)) {
            return;
        }

        $currentTransPattern = '/^translation(@|\.[a-zA-z0-9\.]*@).*$/';

        $blackList = array_flip($this->blackList);

        foreach ($catalogue->getDomains() as $domain) {
            $messages = [];
            foreach ($catalogue->all($domain) as $key => $message) {
                if (!isset($blackList[$key])) {
                    $messages[$key] = $message;
                }
            }

            $catalogue->replace($messages, $domain);
        }

        foreach (array_keys($this->whiteList) as $domain) {
            $extraMessages = [];
            foreach ($this->whiteList[$domain] as $extraMessage) {
                if (preg_match($currentTransPattern, $extraMessage)) {
                    $extraMessages[$extraMessage] = $this->prefix.$extraMessage;
                }
            }
            $catalogue->add($extraMessages, $domain);
        }
    }
}
