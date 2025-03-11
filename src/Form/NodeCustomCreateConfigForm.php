<?php

namespace Drupal\node_custom_create\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configuration form to set the auth token.
 */
class NodeCustomCreateConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['node_custom_create.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'node_custom_create_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('node_custom_create.settings');

    $form['auth_token'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Authentication Token'),
      '#default_value' => $config->get('auth_token'),
      '#required' => TRUE,
      '#description' => $this->t('Enter the token that will be used to authenticate external requests.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('node_custom_create.settings')
      ->set('auth_token', $form_state->getValue('auth_token'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
