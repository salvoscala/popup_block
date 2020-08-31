<?php

namespace Drupal\popup_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\file\Entity\File;

/**
 * Provides a 'popup block' block.
 *
 * @Block(
 *   id = "popup_block",
 *   admin_label = @Translation("Popup Block"),
 *
 * )
 */
class PopupBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $query = \Drupal::database()->select('simple_popup_blocks', 's');
    $query->addField('s', 'status');
    $query->condition('identifier', 'popupblock');
    $status = $query->execute()->fetchField();
    if (!$status) {
      // Do not add the block if popup is disabled.
      // This will improve performances.
      return [];
    }

    $default_value = \Drupal::state()->get('popup_block', []);

    $url = $default_value['url'] ?? NULL;

    return [
      '#theme' => 'popup_block',
      '#url' => $url,
      '#attached' => [
        'library' => ['popup_block/popup_block'],
      ],
    ];
  }
}
