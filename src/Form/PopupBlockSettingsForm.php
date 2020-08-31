<?php

namespace Drupal\popup_block\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\Core\Cache\Cache;

class PopupBlockSettingsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'popup_block_settings_form';
  }


  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $default_value = \Drupal::state()->get('popup_block', []);

    $default_image = NULL;
    if (isset($default_value['image']) && !empty($default_value['image'])) {
      $default_image = $default_value['image'];
    }

    $validators = array(
      'file_validate_extensions' =>['jpg jpeg png gif'],
    );

    $form['image'] = [
      '#type' => 'managed_file',
      '#name' => 'my_file',
      '#title' => t('Popup Image'),
      '#size' => 20,
      '#upload_validators' => $validators,
      '#upload_location' => 'public://',
      '#default_value' => $default_image,
    ];

    $form['url'] = array(
      '#type' => 'textfield',
      '#title' => t('Url'),
      '#default_value' => $default_value['url'] ?? NULL,
    );

    $form['help'] = [
      '#markup' => t('<p>You need to manage popup configuration here: <a href="/admin/config/media/simple_popup_blocks/manage">Manage Popup</a>.</p><p>If you have not created a popup yet you should:<p>
        <p>1. Add the "Popup Block" in the block list: <a href="/admin/structure/block">block list</a></p><p>2.Create a simple popup block here: <a href="/admin/config/media/simple_popup_blocks/add">add popup</a>, selecting the "popupblock" block</p>'),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('Save Block'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    // Set file as permement. This will not remove file at cron.
    if ($values['image']) {
      $image = $values['image'];
      $file = File::load( $image[0] );
      $file->setPermanent();
      $file->save();
    }

    $saved = [
      'image' => $values['image'] ?? NULL,
      'url' => $values['url'] ?? NULL,
    ];

    \Drupal::state()->set('popup_block', $saved);

    \Drupal::messenger()->addMessage(t('The configuration has been saved!'), 'status');
    // Remove block cache.
    Cache::invalidateTags(['config:block.block.popupblock']);
  }

}
