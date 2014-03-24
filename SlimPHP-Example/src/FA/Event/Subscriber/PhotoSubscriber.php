<?php

namespace FA\Event\Subscriber;

use FA\Event\PhotoEvent;
use FA\Paginator\Adapter\DbAdapter as PaginatorAdapter;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zend\Cache\Storage\ClearByPrefixInterface;
use Zend\Cache\Storage\StorageInterface;

class PhotoSubscriber implements EventSubscriberInterface
{
    /**
     * @var AbstractAdapter
     */
    protected $cache;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Public constructor
     *
     * @param AbstractAdapter Cache adapter
     */
    public function __construct($cache, LoggerInterface $logger)
    {
        // Have to do this jankie checking b/c I can't typehint on more than one interface
        $clearByPrefix = $cache instanceof ClearByPrefixInterface;
        $storage = $cache instanceof StorageInterface;

        if (!$clearByPrefix || !$storage) {
            throw new \Exception(
                'Cache must implement both Zend\Cache\Storage\ClearByPrefixInterface ' .
                'and Zend\Cache\Storage\StorageInterface interfaces.'
            );
        }

        $this->cache = $cache;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return array(
            'photo.save' => array('onPhotoSave', 0),
            'photo.delete' => array('onPhotoDelete', 0),
        );
    }

    public function onPhotoSave(PhotoEvent $event)
    {
        $this->logger->info(sprintf('Calling %s for %s', __METHOD__, $event->getName()));
        $this->cache->clearByPrefix(PaginatorAdapter::CACHE_KEY_PREFIX);
    }

    public function onPhotoDelete(PhotoEvent $event)
    {
        $this->logger->info(sprintf('Calling %s for %s', __METHOD__, $event->getName()));
        $this->cache->removeItem($event->getPhoto()->getPhotoId());
        $this->cache->clearByPrefix(PaginatorAdapter::CACHE_KEY_PREFIX);
    }
}
