<?php

namespace Drupal\node_custom_create\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\node\Entity\Node;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller to handle external POST requests for node creation.
 */
class NodeCustomCreateController extends ControllerBase {
  /**
   * Config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory')
    );
  }

  /**
   * Accepts the POST data to create a node.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The HTTP request.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response.
   */
  public function createNode(Request $request) {
    // Fetch the configured auth token.
    $config = $this->configFactory->get('node_custom_create.settings');
    $auth_token = $config->get('auth_token');

    // Get the Authorization header.
    $headers = $request->headers->all();

    // Check if the Authorization header exists.
    if (!isset($headers['authorization'][0])) {
      return new JsonResponse(['message' => 'Authorization header missing'], 400);
    }

    // Extract the Bearer token from the Authorization header.
    $authorization = $headers['authorization'][0];
    $token = null;

    // Check if the Authorization header starts with "Bearer ".
    if (preg_match('/^Bearer\s(\S+)$/', $authorization, $matches)) {
      $token = $matches[1];
    }

    // If the token is not provided or is incorrect.
    if (!$token || $token !== $auth_token) {
      return new JsonResponse(['message' => 'Unauthorized'], 401);
    }

    // Now handle the logic for creating the node.
    $input_data = json_decode($request->getContent(), TRUE);

    // Check if required fields are present.
    if (empty($input_data['title']) || empty($input_data['body'])) {
      return new JsonResponse(['message' => 'Missing required fields'], 400);
    }

    // Create the node.
    $node = Node::create([
      'type' => 'article',
      'title' => $input_data['title'],
      'body' => [
        'value' => $input_data['body'],
        'format' => 'full_html',
      ],
      'field_tags' => [$input_data['tag']],
      'field_image' => ['uri' => $input_data['image_url']],
    ]);
    $node->save();

    // Send email or Notify 3rd party via API.
    // Optionally clear the cache for the node listing page.
    Cache::invalidateTags(['node_list']);

    return new JsonResponse(['message' => 'Node created successfully', 'node_id' => $node->id()]);
  }

}
