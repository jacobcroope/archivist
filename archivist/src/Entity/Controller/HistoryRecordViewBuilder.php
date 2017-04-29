<?php

namespace Drupal\archivist\Entity\Controller;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityViewBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a list controller for the History Record entity.
 *
 * @ingroup content_entity_example
 */
class HistoryRecordViewBuilder extends EntityViewBuilder {
//$view_builder = \Drupal::entityManager()->getViewBuilder('archivist_history_record');

public function view(EntityInterface $entity, $view_mode = 'full', $langcode = NULL) {

	
    $build_list = $this->viewMultiple(array($entity), $view_mode, $langcode);

    // The default ::buildMultiple() #pre_render callback won't run, because we
    // extract a child element of the default renderable array. Thus we must
    // assign an alternative #pre_render callback that applies the necessary
    // transformations and then still calls ::buildMultiple().
    $build = $build_list[0];
	// getFieldDefinition() worked for a general set of information. 

    $build['history_record_file_display'] = array(
      '#markup' => $this->t('<div id="history_record_file_display">' . serialize($build_list) . '</div>', 
      array(
        //'@adminlink' => $this->urlGenerator->generateFromRoute('archivist.history_record_settings'),
      )),
    );
    $build['#pre_render'][] = array($this, 'build');

    return $build;
  }
}

  // /**
//    * The url generator.
//    *
//    * @var \Drupal\Core\Routing\UrlGeneratorInterface
//    */
//   protected $urlGenerator;
// 
// 
//   /**
//    * {@inheritdoc}
//    */
//   public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
//     return new static(
//       $entity_type,
//       $container->get('entity.manager')->getStorage($entity_type->id()),
//       $container->get('url_generator')
//     );
//   }
// 
//   /**
//    * Constructs a new HistoryRecordListBuilder object.
//    *
//    * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
//    *   The entity type definition.
//    * @param \Drupal\Core\Entity\EntityStorageInterface $storage
//    *   The entity storage class.
//    * @param \Drupal\Core\Routing\UrlGeneratorInterface $url_generator
//    *   The url generator.
//    */
//   public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, UrlGeneratorInterface $url_generator) {
//     parent::__construct($entity_type, $storage);
//     $this->urlGenerator = $url_generator;
//   }
// 
// 
//   /**
//    * {@inheritdoc}
//    *
//    * We override ::render() so that we can add our own content above the table.
//    * parent::render() is where EntityListBuilder creates the table using our
//    * buildHeader() and buildRow() implementations.
//    */
//   public function render() {
//     $build['description'] = array(
//       '#markup' => $this->t('History Records are listed here for all users. You can manage the fields on the <a href="@adminlink">History Record Admin Page</a>.', array(
//         '@adminlink' => $this->urlGenerator->generateFromRoute('archivist.history_record_settings'),
//       )),
//     );
//     $build['table'] = parent::render();
//     return $build;
//   }
// 
//   /**
//    * {@inheritdoc}
//    *
//    * Building the header and content lines for the history record list.
//    *
//    * Calling the parent::buildHeader() adds a column for the possible actions
//    * and inserts the 'edit' and 'delete' links as defined for the entity type.
//    */
//   public function buildHeader() {
//     $header['id'] = $this->t('History Record ID');
// 	$header['name'] = $this->t('Title');
// 	$header['subject'] = $this->t('Subject');
// 	$header['submitting_instution'] = $this->t('Submitting Institution');
// 	$header['upload_date'] = $this->t('Upload Date');
// 	
//     
//     return $header + parent::buildHeader();
//   }
// 
//   /**
//    * {@inheritdoc}
//    */
//   public function buildRow(EntityInterface $entity) {
//     /* @var $entity \Drupal\archivist\Entity\HistoryRecord */
//     $row['id'] = $entity->id();
//     $row['name'] = $entity->link();
//     $row['subject'] = $entity->subject->value;
//     $row['submitting_institution'] = $entity->submitting_institution->value;
//     $row['upload_date'] = $entity->upload_date->value;
//     return $row + parent::buildRow($entity);
//   }
//}
