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

    private function getUserId() {
        $regex = "/mentor-details\/([0-9]+)\//";
        $pageUri = $_SERVER['REQUEST_URI'];
        preg_match($regex, $pageUri, $matches);
        return intval($matches[1]);
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
        $userInfo = $this->getMentorInfo();
        print_r($userInfo);

        $html = "
        <div class='mentor-details'>
            hihihi    
        </div>
        ";

        return $html;
    }

    private function getMentorInfo() {
        $mentors = $this->getMentors();

        foreach($mentors as $mentor) {
            if ($mentor->ID === $this->userId) {
                $userMetaData = get_user_meta($mentor->ID);

                return [
                    "id" => $mentor->ID,
                    "avatar" => get_avatar_url($mentor->user_email),
                    "display_name" => $mentor->display_name,
                    "company" => $userMetaData["company"][0],
                    "title" => $userMetaData["title"][0],
                    "email" => $mentor->user_email,
                    "phone" => $userMetaData["phone"][0],
                    "biography" => $userMetaData["biography"][0],
                    "year_of_experience" => $userMetaData["year_of_experience"][0],
                    "degree" => $userMetaData["degree"][0],
                    "career_field" => $userMetaData["career_field"][0],
                    "expertise" => $userMetaData["expertise"][0],
                    "topics" => $userMetaData["topics"][0],
                    "hobbies" => $userMetaData["hobbies"][0],
                    "mentoring_program" => $userMetaData["mentoring_program"][0],
                ];
            }
        }

        echo "cannot find user";
        return [];
    }

    private function getMentors() {
        return get_users([
            'role' => 'mentor',
            'orderby' => 'user_registered',
            'order' => 'ASC'
        ]);
    }
}