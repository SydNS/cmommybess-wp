<?php
defined('ABSPATH') or die;

class CFormFields {
    public $fields = array();

    /**
     * Parse fields from Nicepage publishHtml
     *
     * @param string $form_html
     */
    public function parseFromHtml($form_html) {
        preg_match_all('#<(input|textarea)([^>]*)>#', $form_html, $matches);

        for ($i = 0; $i < count($matches[0]); $i++) {
            $attrs = $matches[2][$i];
            if (!preg_match('#name=(\'|")([^\'"]*)#', $attrs, $m) || strpos($attrs, 'type="hidden"') !== false) {
                continue;
            }
            $name = $m[2];

            if ($name === 'name') { // see detect_unavailable_names
                $name = 'name1';
            }
            $required = strpos($attrs, 'required') !== false;

            $this->fields[] = array(
                'required' => $required,
                'name' => $name,
            );
        }
    }

    /**
     * Convert to contact7 format
     *
     * @return string
     */
    public function toString() {
        if (!function_exists('_arr')) {
            /**
             * Get array value by specified key
             *
             * @param array      $array
             * @param string|int $key
             * @param mixed      $default
             *
             * @return mixed
             */
            function _arr(&$array, $key, $default = false) {
                if (isset($array[$key])) {
                    return $array[$key];
                }
                return $default;
            }
        }
        $result = '';
        foreach ($this->fields as $field) {
            $result .= sprintf("[%s%s %s]\n", _arr(self::$_nameTags, $field['name'], 'text'), $field['required'] ? '*' : '', $field['name']);
        }
        $result .= "[submit]\n";
        return $result;
    }

    /**
     * Check for existing field with such name
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasField($name) {
        foreach ($this->fields as $field) {
            if ($field['name'] === $name) {
                return true;
            }
        }
        return false;
    }

    private static $_nameTags = array(
        'email' => 'email',
        'tel' => 'tel',
        'message' => 'textarea',
    );
}

class CForms {
    /**
     * Update forms data sources
     * Create new if needed
     *
     * @param array $forms
     * @param string $template
     *
     * @return array form data sources array
     */
    public static function _updateForms($forms, $template) {
        if (!class_exists('WPCF7_ContactForm')) {
            return array();
        }
        $count = count($forms);
        $prev_data_sources = "";
        if ($template === "header") {
            $prev_data_sources = get_option('header_forms_theme');
        }
        if ($template === "footer") {
            $prev_data_sources = get_option('footer_forms_theme');
        }
        $data_sources = array();

        for ($i = 0; $i < $count; $i++) {
            $form_html = $forms[$i];
            $form_id = isset($prev_data_sources[$i]['id']) ? $prev_data_sources[$i]['id'] : 0;
            $contact_form = null;

            if ($form_id) {
                $contact_form = wpcf7_contact_form($form_id);
            }

            if (!$form_id || !$contact_form) {
                $form_id = 0;
                $contact_form = WPCF7_ContactForm::get_template();
                $form_title = "";
                if ($template === "footer") {
                    $form_title = sprintf(__('Form: %s', 'craftymommybeeswp'), "Footer");
                }
                if ($template === "header") {
                    $form_title = sprintf(__('Form: %s', 'craftymommybeeswp'), "Header");
                }
                if ($count > 1) {
                    $form_title .= ' (' . ($i + 1) . ')';
                }
                $contact_form->set_title($form_title);
            }

            $fields = new CFormFields();
            $fields->parseFromHtml($form_html);

            $properties = $contact_form->get_properties();
            $properties['form'] = $fields->toString();

            if (!$form_id) {
                foreach (array('mail', 'mail_2') as $mail_key) {
                    foreach ($properties[$mail_key] as &$prop) {
                        if (is_string($prop)) {
                            $prop = str_replace(
                                array(
                                    '"[your-subject]"',
                                    "Subject: [your-subject]\n",
                                    '[your-email]',
                                    '[your-name]',
                                    '[your-message]',
                                ),
                                array(
                                    __('feedback', 'craftymommybeeswp'),
                                    '',
                                    '[email]',
                                    $fields->hasField('name1') ? '[name1]' : '',
                                    $fields->hasField('message') ? '[message]' : '',
                                ),
                                $prop
                            );
                        }
                    }
                }
            }
            $contact_form->set_properties($properties);

            $form_id = $contact_form->save();

            $data_sources[] = array(
                'id' => $form_id,
            );
        }
        if ($template === "header") {
            update_option('header_forms_theme', $data_sources);
        }
        if ($template === "footer") {
            update_option('footer_forms_theme', $data_sources);
        }
        return $data_sources;
    }

    public static $_formHtml;
    public static $_formIdx = 0;

    /**
     * Filter on wpcf7_form_elements
     * Replace default contact7 fields with Np fields
     *
     * @param string $html
     *
     * @return string
     */
    public static function _formElementsFilter($html) {
        $fields_html = preg_replace('#<form\s[^>]*>#', '', self::$_formHtml);
        $fields_html = str_replace('</form>', '', $fields_html);
        $html = str_replace(
            array(
                'name="name"',
                'u-input ',
                'u-form-submit',
                'u-form-group',
                'u-btn ',
            ),
            array(
                'name="name1"',
                'u-input wpcf7-form-control ',
                'u-form-submit wpcf7-form-control',
                'u-form-group wpcf7-form-control-wrap',
                'u-btn wpcf7-submit '
            ),
            $fields_html
        );
        $html .= '<input type="hidden" name="_contact7_backend" value="1">';
        return $html;
    }

    /**
     * Process Np form html
     *
     * @param string|int $form_id
     * @param string     $form_raw_html
     *
     * @return string
     */
    public static function getHtml($form_id, $form_raw_html) {
        if (function_exists('wpcf7_contact_form') && $form_id && ($contact_form = wpcf7_contact_form($form_id))) {
            self::$_formHtml = $form_raw_html;

            add_filter('wpcf7_form_elements', 'CForms::_formElementsFilter', 9);
            add_filter('wpcf7_form_novalidate', '__return_false');

            $form_class = '';
            if (preg_match('#<form.*?class="([^"]*)#', $form_raw_html, $m)) {
                $form_class = $m[1];
            }
            $form_html = $contact_form->form_html(array('html_class' => $form_class . ' u-form-custom-backend'));
            if (strpos($form_raw_html, 'redirect="true"') !== false && preg_match('#redirect-address="([^"]*)"#', $form_raw_html, $m)) {
                $form_html = str_replace('<form', '<form redirect-address="' . $m[1] . '"', $form_html);
            }

            remove_filter('wpcf7_form_elements', 'CForms::_formElementsFilter', 9);
            remove_filter('wpcf7_form_novalidate', '__return_false');
        } else {
            $form_html = preg_replace('#action="[^"]*#', 'action="#', $form_raw_html);
        }
        if (self::$_formIdx === 0) {
            $form_html = CForms::getScriptsAndStyles() . "\n" . $form_html;
        }
        self::$_formIdx++;
        return $form_html;
    }

    /**
     * Common scripts and styles for all forms
     *
     * @return string
     */
    public static function getScriptsAndStyles() {
        ob_start();
        ?>
        <script>
            function onSuccess(event) {
                var msgContainer = jQuery(event.currentTarget).find('.wpcf7-response-output');
                msgContainer.removeClass('u-form-send-error').addClass('u-form-send-message u-form-send-success');
                msgContainer.show();
                var redirectAddress = jQuery(event.currentTarget).find('[redirect-address]').attr('redirect-address');
                if (redirectAddress) {
                    location.replace(redirectAddress);
                }
            }
            function onError(event) {
                var msgContainer = jQuery(event.currentTarget).find('.wpcf7-response-output');
                msgContainer.removeClass('u-form-send-success').addClass('u-form-send-message u-form-send-error');
                msgContainer.show();
            }

            jQuery('body')
                .on('wpcf7mailsent',   '.u-form .wpcf7', onSuccess)
                .on('wpcf7invalid',    '.u-form .wpcf7', onError)
                .on('wpcf7:unaccepted', '.u-form .wpcf7', onError)
                .on('wpcf7spam',       '.u-form .wpcf7', onError)
                .on('wpcf7:aborted',    '.u-form .wpcf7', onError)
                .on('wpcf7mailfailed', '.u-form .wpcf7', onError);
        </script>
        <style>
            .u-form .wpcf7-response-output {
                /*position: relative !important;*/
                margin: 0 !important;
                bottom: -70px!important;
            }
        </style>
        <?php
        return ob_get_clean();
    }

    /**
     * Filter on wpcf7_ajax_json_echo
     * Replace selectors for Np forms
     *
     * @param array $items
     *
     * @return array
     */
    public static function _ajaxJsonEchoFilter($items) {
        if (isset($_POST['_contact7_backend']) && !empty($items['invalids'])) {
            foreach ($items['invalids'] as &$invalid) {
                $invalid['into'] = str_replace('span.wpcf7-form-control-wrap.', 'div.u-form-group.u-form-', $invalid['into']);
            }
        }
        return $items;
    }
}

add_filter('wpcf7_ajax_json_echo', 'CForms::_ajaxJsonEchoFilter');