<?php
if (!defined('ABSPATH')) {
    die;
}

class VamMentorView {
    private $enqueueHandleName = "vammentor-view";
    private $mentors;

    function __construct() {
        $this->mentors = $this->getMentors();
      }

    public static function init() {
        $self = new self();
        // Include js css file
        add_action('wp_enqueue_scripts', [$self, 'importCss']);
        add_action('wp_enqueue_scripts', [$self, 'importJs']);

        add_shortcode( 'vammentor', [$self, 'getTemplate'] );
    }

    public function importCss() {
        wp_enqueue_style($this->enqueueHandleName, DIR_PLUGIN . "/assets/css/mentor_view.css");
    }

    public function importJs() {
        wp_enqueue_script("jquery");
        wp_enqueue_script($this->enqueueHandleName, DIR_PLUGIN . "/assets/js/mentor-view.js", [], false, true);
        $this->sendDataToUseInJavascriptFiles();
    }

    private function sendDataToUseInJavascriptFiles() {
        wp_localize_script( $this->enqueueHandleName, "phpMentors", $this->mentors );
    }

    private function getMentors() {
        return get_users([
            'role' => 'mentor',
            'orderby' => 'user_registered',
            'order' => 'ASC'
        ]);
    }

    public function getTemplate() {
        $html = "
        <div class='mentor-view'>
            <h2 class='mentor-view__heading elementor-heading-title elementor-size-default'>DANH SÁCH MENTOR</h2>
            <div class='mentor-view__filter-container'>
                <h4 class='mentor-view__filter-description'>Lọc theo:</h4>

                <div class='mentor-view__select-container'>
                    <select class='mentor-view__select'>
                        <option value=''>Chương trình mentoring</option>
                        <option>B</option>
                        <option>C</option>
                    </select>
                </div>

                <div class='mentor-view__select-container'>
                    <select class='mentor-view__select'>
                        <option value=''>Lĩnh vực chia sẻ</option>
                        <option>B</option>
                        <option>C</option>
                    </select>
                </div>
            </div>
        ";

        $html .= "<div class='mentor-view__mentors'><ul class='mentor-view__list'>";
        
        foreach ($this->mentors as $user) {
            $html .= $this->getMentorHTML($user);
        }

        $html .= '</ul></div></div>';
        return $html;
    }

    private function getMentorHTML($user) {
        $userMetaData = get_user_meta($user->ID);

        return "
        <li class='mentor-view__list-item'>
            <img class='mentor-view__avatar' src='" . get_avatar_url($user->ID) . "' />
            <h5 class='mentor-view__name'>$user->display_name</h5>
            <p><strong>" . $userMetaData['company'][0] . "</strong></p>
            <p><strong>" . $userMetaData['title'][0] . "</strong></p>
            <p class='mentor-view__topics'><strong>Lĩnh vực chia sẻ:</strong> " . $userMetaData['topics'][0] . "<p>
        </li>";
    }
}