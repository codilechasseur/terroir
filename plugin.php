<?php

/**
 * Plugin Name: Terroir
 * Plugin URI:  https://github.com/codilechasseur/terroir
 * Description: An opinionated extension of Bedrock.
 * Version:     1.0.0
 * Author:      Codi Lechasseur
 * Author URI:  https://github.com/codilechasseur
 * Licence:     MIT
 */

use Illuminate\Support\Str; # This belongs to modern-acf-options

add_action('plugins_loaded', new class
{
    /**
     * Invoke the plugin.
     *
     * @return void
     */
    public function __invoke()
    {
        /**
         * Change the WordPress login header to the blog name
         *
         * @return string
         */
        add_filter('login_headertext', function () {
            return get_bloginfo('name');
        });

        /**
         * Change the WordPress login header URL to the home URL
         *
         * @return string
         */
        add_filter('login_headerurl', function () {
            return get_home_url();
        });

        /**
         * Change the WordPress login colour palette
         *
         * @return array
         */
        add_filter('login_color_palette', function () {
            return [
                'brand'    => '#0073aa',
                'trim'     => '#181818',
                'trim-alt' => '#282828',
            ];
        });

        /**
         * Hide version on WordPress SEO's HTML output.
         *
         * @return bool
         */
        add_filter('wpseo_hide_version', '__return_true');

        /**
         * Disable the widget block editor.
         *
         * @return bool
         */
        add_filter('gutenberg_use_widgets_block_editor', '__return_false');

        /**
         * Remove failed login logging from Simple History.
         *
         * @param  bool   $logged
         * @param  string $slug
         * @param  string $key
         * @param  int    $level
         * @param  string $context
         * @return bool
         */
        add_filter('simple_history/simple_logger/log_message_key', function ($logged, $slug, $key, $level, $context) {
            if ($this->contains($slug, 'SimpleUserLogger') && $this->contains($key, ['user_login_failed', 'user_unknown_login_failed'])) {
                return false;
            }

            return $logged;
        }, 10, 5);

        /**
         * Disable RankMath's whitelabeling.
         *
         * @return bool
         */
        foreach([
            'rank_math/whitelabel',
            'rank_math/link/remove_class',
            'rank_math/sitemap/remove_credit',
            'rank_math/frontend/remove_credit_notice',
        ] as $hook) {
            add_filter($hook, '__return_true');
        }

        /**
         * Register the defined Google Maps API key with ACF.
         *
         * @return void
         */
        add_filter('acf/init', function () {
            if (! defined('GOOGLE_MAPS_API_KEY') || ! function_exists('acf_update_setting')) {
                return;
            }

            acf_update_setting('google_api_key', GOOGLE_MAPS_API_KEY);
        });

        // This is modern-acf-options plugin stuff

        /**
         * Remove admin footer text.
         *
         * @return bool
         */
        add_filter('admin_footer_text', '__return_false', 100);

        /**
         * Disable Screen Options on the theme options page.
         *
         * @param  bool       $show
         * @param  \WP_Screen $screen
         * @return bool
         */
        add_filter('screen_options_show_screen', function ($show, $screen) {
            if (is_a($screen, 'WP_Screen') && Str::contains($screen->base, 'theme-options')) {
                return false;
            }
        }, 1, 2);

        add_filter('acf_color_palette', function () {
            return [
                'brand' => '#0073aa',
                'trim' => '#181818',
            ];
        });

        // use StoutLogic\AcfBuilder\FieldsBuilder;

        // acf_add_options_page([
        //     'page_title' => get_bloginfo('name'),
        //     'menu_title' => 'Theme Options',
        //     'menu_slug' => 'theme-options',
        //     'update_button' => 'Update Options',
        //     'capability' => 'edit_theme_options',
        //     'position' => '999',
        //     'autoload' => true,
        // ]);

        // $options = new FieldsBuilder('theme_options', [
        //     'style' => 'seamless',
        // ]);

        // $options
        //     ->setLocation('options_page', '==', 'theme-options');

        // $options
        //     ->addTab('general')
        //         ->setConfig('placement', 'left')

        //         ->addAccordion('customization')
        //         ->addImage('logo')

        //         ->addAccordion('tracking')
        //             ->addText('gtm')
        //                 ->setConfig('label', 'Google Tag Manager')
        //         ->addAccordion('tracking_end')->endpoint()

        //     ->addTab('advanced')
        //         ->setConfig('placement', 'left')

        //         ->addTrueFalse('debug')
        //         ->setConfig('ui', '1');

        // acf_add_local_field_group($options->build());
    }
});
