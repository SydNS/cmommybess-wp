<?php
defined('ABSPATH') or die;

/**
 * Pwizard
 */
class Pwizard {

    protected $options_steps = array();

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct($options) {
        $this->enqueueScripts();
        $this->setOptions($options);
        $this->init();
    }

    /**
     * Set options
     *
     * @param array $options
     */
    public function setOptions($options) {
        if (isset($options['page_slug'])) {
            $this->page_slug = esc_attr($options['page_slug']);
        }
        if (isset($options['page_title'])) {
            $this->page_title = esc_attr($options['page_title']);
        }
    }

    /**
     * Print the content for the widgets step
     */
    public function getStepContent() {
        $content = array();
        // Check if the content imported
        $hideImport = get_option('themler_hide_import_notice');
        if ($hideImport) {
            $content['summary'] = sprintf(
                '<p>%s</p>',
                __('Content has already been imported. Please skip this step', 'nicepage')
            );
        } else {
            $content['summary'] = sprintf(
                '<p>%s</p>',
                __('Nicepage plugin has Pages, Images, Menu, Header, and Footer. </br></br>Do you want to import the Content?', 'nicepage')
            );
        }

        $content = apply_filters('pwizard_filter_content', $content);
        return $content;
    }

    /**
     * Print the content for the final step
     */
    public function getStepDone() {
        $content = array();
        $content['summary'] = sprintf(
            '<p>%s</p>',
            __('Congratulations! The Nicepage plugin has been activated and your website is ready.', 'nicepage')
        );
        $content['summary'] .= sprintf('<p>%s</p>', 'Create a new page with the Nicepage Editor.', 'nicepage');
        $content['buttons'] = '<br><a href="' . admin_url('post-new.php?post_type=page&np_new=1') . '" class="button button-primary">Create Page</a>';
        $content['buttons'] .= '<a href="' . get_site_url() . '" style="margin-left: 5px;" id="visit-site" class="button button-secondary">Visit Site</a>';
        $content['buttons'] .= '<a href="' . get_admin_url() . '" style="margin-left: 5px;" id="visit-site" class="button button-secondary">Close</a>';
        return $content;
    }

    /**
     * Set options for the steps
     *
     * @return array
     */
    public function getSteps() {
        $steps = array(
            'done' => array(
                'id' => 'done',
                'title' => __('Your website is ready!', 'nicepage'),
                'icon' => 'yes',
                'view' => 'getStepDone',
                'callback' => ''
            )
        );
        $import_content_step = array(
            'content' => array(
                'id' => 'content',
                'title' => __('Import Content', 'nicepage'),
                'icon' => 'welcome-content-menus',
                'view' => 'getStepContent',
                'callback' => 'import_content',
                'callback2' => 'replace_content',
                'button_text' => __('Import Content', 'nicepage'),
                'button2_text' => __('Replace previously imported Content', 'nicepage'),
                'can_skip' => true,
                'can_replace' => true
            ),
        );
        if (file_exists(dirname(dirname(__FILE__)) . '/content/content.json')) {
            $steps = $import_content_step + $steps;
        }
        return $steps;
    }

    /**
     * Make an interface for the wizard
     */
    public function wizardPage() {
        ?>
        <div class="wrap pwizard-wrap-perent">
            <?php
            echo '<div class="card pwizard-wrap">';
            $steps = $this->getSteps();
            echo '<ul class="pwizard-menu">';
            foreach ($steps as $step) {
                $class = 'step step-' . esc_attr($step['id']);
                echo '<li data-step="' . esc_attr($step['id']) . '" class="' . esc_attr($class) . '">';
                printf('<h2>%s</h2>', esc_html($step['title']));
                // $content split
                $content = call_user_func(array($this, $step['view']));
                if (isset($content['summary'])) {
                    printf(
                        '<div class="summary">%s</div>',
                        wp_kses_post($content['summary'])
                    );
                }
                if (isset($content['buttons'])) {
                    echo $content['buttons'];
                }
                if (isset($content['detail'])) {
                    printf(
                        '<div class="detail">%s</div>',
                        $content['detail'] // Need to escape this
                    );
                }
                // Next button
                if (isset($step['button_text']) && $step['button_text']) {
                    printf(
                        '<div class="button-wrap"><a href="#" class="button button-primary p-do-it" data-callback="%s" data-step="%s">%s</a></div>',
                        esc_attr($step['callback']),
                        esc_attr($step['id']),
                        esc_html($step['button_text'])
                    );
                }
                // Replace button
                if (isset($step['button2_text']) && $step['button2_text']) {
                    printf(
                        '<div class="button-wrap" style="margin-left: 0.5em;"><a href="#" class="button button-secondary p-do-it" data-callback="%s" data-step="%s">%s</a></div>',
                        esc_attr($step['callback2']),
                        esc_attr($step['id']),
                        esc_html($step['button2_text'])
                    );
                }
                // Skip button
                if (isset($step['can_skip']) && $step['can_skip']) {
                    printf(
                        '<div class="button-wrap" style="margin-left: 0.5em;"><a href="#" class="button button-secondary p-do-it" data-callback="%s" data-step="%s">%s</a></div>',
                        'do_next_step',
                        esc_attr($step['id']),
                        __('Skip', 'nicepage')
                    );
                }

                echo '</li>';
            }
            echo '</ul>';
            ?>
            <div class="step-loading"><span class="spinner"></span></div>
            <?php
            if (isset($GLOBALS['npThemeVersion']) && (float)APP_PLUGIN_VERSION > (float)$GLOBALS['npThemeVersion']) {
                // if our theme older then plugin
                echo sprintf('<div class="pwizard-warning"><p>%s</p></div>', 'The active theme has a version lower than the plugin version. Please update the theme too.', 'nicepage');
            }
            ?>
        </div><!-- .pwizard-wrap -->

        </div><!-- .wrap -->
    <?php }

    /**
     * Add styles and scripts
     */
    public function enqueueScripts() {
        wp_register_script('pwizard', APP_PLUGIN_URL . 'importer/assets/js/pwizard.js', array('jquery'), time());
        wp_localize_script(
            'pwizard',
            'pwizard_params',
            array(
                'urlContent'     => admin_url("admin-ajax.php"),
                'wpnonceContent' => wp_create_nonce('np-importer'),
                'actionImportContent'  => 'np_import_content',
                'actionReplaceContent'  => 'np_replace_content',
            )
        );
        wp_enqueue_script('pwizard');
    }

    /**
     * Hooks and filters
     */
    public function init() {
        $this->wizardPage();
        add_action('wp_ajax_setup_content', array($this, 'setup_content'));
    }
}
?>