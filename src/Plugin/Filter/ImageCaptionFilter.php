<?php

namespace Drupal\image_caption\Plugin\Filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;

/**
 * This module looks for <img> tags of the class 'image-left', 'image-right' or
 * 'standalone-image' with a title attibute set. It then places the <img> tag
 * in a <div> with the width and class attributes set. A <div> tag of class
 * "caption" is also added with the caption text obtained from the title. It
 * removes the class attribute from the <img> tag.
 *
 * @Filter(
 *   id = "image_caption",
 *   title = @Translation("Image Caption"),
 *   description = @Translation("Creates captions on images using the title attribute."),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_MARKUP_LANGUAGE,
 *   settings = {
 *     "javascript_status" = "without_js",
 *     "classes" = "caption"
 *   }
 * )
 */
class ImageCaptionFilter extends FilterBase {

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {

    // Ask user if he wants to use Javascript to create the captions.
    $form['javascript_status'] = [
      '#title' => $this->t('Create captions:'),
      '#type' => 'select',
      '#options' => [
        'with_js' => $this->t('With Javascript'),
        'without_js' => $this->t('Without Javascript'),
      ],
      '#default_value' => $this->settings['javascript_status'],
      '#description' => $this->t('Please choose whether you want to use Javascipt to create and display the captions or not.'),
      '#required' => TRUE,
    ];

    // If user selects 'Without Javascript' option, he can choose to add other
    // classes to target as well.
    $form['classes'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Classes to be searched for image captions'),
      '#size' => 80,
      '#default_value' => $this->settings['classes'],
      '#description' => $this->t('Enter a space-separated list of classes. The filter will only operate on images which have one of these CSS classes and have a title attribute.'),
      '#required' => TRUE,
      '#states' => [
        'visible' => [
          ':input[data-classes="javascript_status"]' => ['value' => 'without_js'],
        ],
      ],
    ];

    return $form;
  }

  /**
   * Implements hook_filter_FILTER_process().
   */
  public function process($text, $langcode) {

    if ($this->settings['javascript_status'] == 'with_js') {

      $return_text = new FilterProcessResult($text);
      $return_text->setAttachments([
        'library' => ['image_caption/image_caption'],
      ]);

      return $return_text;
    }
    else {

      $callback = 'addCaptionWithoutJavaScript';
      $this->storeClasses(array_filter(explode(' ', $this->settings['classes'])));
      $text = preg_replace_callback('|(<img.*?>)|s', [$this, $callback], $text);
      return new FilterProcessResult($text);
    }

  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = TRUE) {

    $js_choice = $this->settings['javascript_status'];
    $classes = $this->settings['classes'];
    $display = js_choice == 'with_js' ? $this->t('by using Javascript.') : $this->t('without using Javascript.');

    $output = $this->t('Adds captions, from the title attribute, to images with one of the following classes: %classes %js_choice', ['%classes' => $classes, '%js_choice' => $js_choice]) . '<br />';
    return $output;
  }

  /**
   * Storage for active class names.
   *
   * The addCaptionWithoutJavaScript() function is called by
   * preg_replace_callback() and this function allows only one argument.
   */
  protected function storeClasses($classes = NULL) {
    static $_classes = [];
    if ($classes != NULL) {
      $_classes = $classes;
    }

    return $_classes;
  }

  /**
   * This will add captions without using Javascript.
   */
  protected function addCaptionWithoutJavaScript($img_tag_matches, $active_classes = NULL) {
    $img_tag = $img_tag_matches[0];
    $return_text = $img_tag;

    // Only execute this filter on img tags with (at least) one of the classes
    // we are interested in.
    $has_class = preg_match('/class=\"(.+?)\"/i', $img_tag, $matches) > 0;
    if ($has_class) {
      $class = $matches[1];

      /*
       * Formally, class is a space separated list of classes, but we allow all
       * horizontal whitespace in any quantity. That's why we use preg_split
       * instead of explode.
       */
      $classes = preg_split('/\s+/', $class, NULL, PREG_SPLIT_NO_EMPTY);

      // Get active classes via storeClasses() because preg_replace_callback()
      // does not support addional arguments.
      if ($active_classes == NULL) {
        $active_classes = $this->storeClasses();
      }

      if (count(array_intersect($classes, $active_classes)) > 0) {
        // Only execute this filter on img tags that have a title attribute.
        $has_title = preg_match('/title=\"(.+?)\"/i', $img_tag, $matches) > 0;
        if ($has_title) {
          $title = nl2br(html_entity_decode($matches[1]));

          // Search for width specified as an inline style or width attribute,
          // if no width specified, don't output it on the outer figcaption,
          // assume width will be handled with css external to this module /
          // filter.
          $width = '';
          if (preg_match('/width:\s*(\d+)px/i', $img_tag, $matches) == 1 || preg_match('/width=\"(\d+?)\"/i', $img_tag, $matches) == 1) {
            $width = $matches[1];
          }

          // Search for float specified as an inline style on the image.
          $float = '';
          if (preg_match('/float:\s*(\w+)/i', $img_tag, $matches) == 1) {
            $float = $matches[1];
          }

          // Remove the class from the image tag.
          $img_tag = preg_replace('/class=\"(.+?)\"/i', '', $img_tag);

          // Build the image and caption.
          $caption = [
            'img' => [
              '#type' => 'markup',
              '#markup' => $img_tag,
            ],
            'caption' => [
              '#type' => 'html_tag',
              '#tag' => 'figcaption',
              '#attributes' => [
                'class' => 'caption',
                'style' => 'display:block',
              ],
              '#value' => $title,
            ],
          ];

          // Build the wrapping element.
          $element = [
            'image_caption' => [
              '#type' => 'html_tag',
              '#tag' => 'figure',
              '#attributes' => [
                'class' => $class,
                'style' => 'display:block',
              ],
              '#value' => render($caption),
            ],
          ];

          if (!empty($width)) {
            $element['image_caption']['#attributes']['style'][] = 'width:' . $width . 'px;';
          }

          if (!empty($float)) {
            $element['image_caption']['#attributes']['style'][] = 'float:' . $float . ';';
          }

          $return_text = render($element);

        }
      }
    }
    return $return_text;
  }

}
