<?php
if (!defined('ABSPATH')) {
    die;
}

class VamMentorDetails {
    private $enqueueHandleName = "vammentor-details";

    public static function init() {
        $self = new self();
        add_action('wp_enqueue_scripts', [$self, 'importCss']);
        add_shortcode( 'vammentor_details', [$self, 'getTemplate'] );
    }

    public function importCss() {
        wp_enqueue_style($this->enqueueHandleName, DIR_PLUGIN . "/assets/css/mentor_details.css");
    }

    public function getTemplate() {
        $html = "
        <div class='mentor-details'>
            hihihi    
        </div>
        ";

        return $html;
    }
}