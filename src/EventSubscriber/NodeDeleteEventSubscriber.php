<?php

namespace Drupal\node_custom_create\EventSubscriber;

use Drupal\Core\Logger\LoggerChannelFactory;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\NodeInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\core_event_dispatcher\Event\Entity\EntityDeleteEvent;

/**
 * Node delete event subscriber.
 */
class NodeDeleteEventSubscriber implements EventSubscriberInterface {

  use StringTranslationTrait;

  /**
   * The logger channel factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactory
   */
  protected $loggerFactory;

  /**
   * Constructor for NodeDeleteEventSubscriber.
   *
   * @param \Drupal\Core\Logger\LoggerChannelFactory $logger_factory
   *   The logger channel factory service.
   */
  public function __construct(LoggerChannelFactory $logger_factory) {
    $this->loggerFactory = $logger_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      EntityHookEvents::ENTITY_DELETE => 'onNodeDelete',
    ];
  }

  /**
   * Logs a message when a node is deleted.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityDeleteEvent $event
   *   The node delete event.
   */
  public function onNodeDelete(EntityDeleteEvent $event) {
    $entity = $event->getEntity();

    // Only react to node entities of type 'article'.
    if ($entity instanceof NodeInterface && $entity->getType() === 'article') {
      $this->loggerFactory->get('node_custom_create')->notice('Node deleted: @title (ID: @nid)', [
        '@title' => $entity->getTitle(),
        '@nid' => $entity->id(),
      ]);
    }

    // Send email or notify 3rd party via API here etc.
  }
}
