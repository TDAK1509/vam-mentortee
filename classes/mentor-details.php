<?php
if (!defined('ABSPATH')) {
    die;
}

class VamMentorDetails {
    private $enqueueHandleName = "vammentor-details";
    private $userId;
    
    function __construct() {
        $this->userId = $this->getUserId();
    }

    public static function init() {
        $self = new self();
        add_action('wp_enqueue_scripts', [$self, 'importCss']);
        add_shortcode( 'vammentor_details', [$self, 'getTemplate'] );
    }

    public function importCss() {
        wp_enqueue_style($this->enqueueHandleName, DIR_PLUGIN . "/assets/css/mentor_details.css");
    }

    public function getTemplate() {
        $userId = $this->userId;
        echo $userId . "<br>";

        $html = "
        <div class='mentor-details'>
            hihihi    
        </div>
        ";

        return $html;
    }

    private function getUserId() {
        $regex = "/mentor-details\/([0-9]+)\//";
        $pageUri = $_SERVER['REQUEST_URI'];
        preg_match($regex, $pageUri, $matches);
        return $matches[1];
    }
}