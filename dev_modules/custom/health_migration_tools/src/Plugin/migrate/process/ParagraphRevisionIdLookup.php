<?php

/**
 * @file
 *
 */

namespace Drupal\health_migration_tools\Plugin\migrate\process;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Database\Connection;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Perform lookup of an existing Paragraph.
 *
 * @MigrateProcessPlugin(
 *   id = "health_migration_tools_paragraph_revision_id_lookup"
 * )
 *
 * To do custom value transformations use the following:
 *
 * @code
 * field_text:
 *   plugin: health_migration_tools_paragraph_revision_id_lookup
 *   source: entity_id
 * @endcode
 *
 */
class ParagraphRevisionIdLookup extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Class constructor method.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Connection $connection) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->connection = $connection;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $pluginId, $pluginDefinition) {
    $instance = new static(
      $configuration,
      $pluginId,
      $pluginDefinition,
      $container->get('database'),
    );

    return $instance;
  }


  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $paragraph_id = $value;
    $paragraph_revision_id = '';

    // Lookup the paragraph revision ID based on the paragraphs's entity ID.
    $query = $this->connection->select('paragraphs_item', 'pi');
    $results = $query
      ->fields('pi', ['revision_id'])
      ->condition('pi.id', $paragraph_id)
      ->execute();

    $record = $results->fetchObject();

    if (!empty($record) && isset($record->revision_id)) {
      $paragraph_revision_id = $record->revision_id;
    }
    else {
      $paragraph_revision_id = NULL;
    }

    return $paragraph_revision_id;
  }
}
