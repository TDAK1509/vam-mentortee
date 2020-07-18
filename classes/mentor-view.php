<?php
if (!defined('ABSPATH')) {
    die;
}

class VamMentorView {
    private $enqueueHandleName = "vammentor-view";
    private $mentors;

    function __construct() {
        $this->mentors = $this->getMentorsInfo();
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

    private function getMentorsInfo() {
        $mentors = $this->getMentors();
        $mentorsInfo = [];

        foreach($mentors as $mentor) {
            $userMetaData = get_user_meta($mentor->ID);

            $mentorInfo = [
                "avatar" => get_avatar_url($mentor->user_email),
                "display_name" => $mentor->display_name,
                "company" => $userMetaData["company"][0],
                "title" => $userMetaData["title"][0],
                "mentoring_program" => $userMetaData["mentoring_program"][0],
                "expertise" => $userMetaData["expertise"][0],
            ];

            array_push($mentorsInfo, $mentorInfo);
        }

        return $mentorsInfo;
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
                    <select class='mentor-view__select' id='mentoring_programs'>
                        " . $this->getMentoringProgramOptionsHTML() . "
                    </select>
                </div>

                <div class='mentor-view__select-container'>
                    <select class='mentor-view__select' id='expertises'>
                        " . $this->getExpertiseOptionsHTML() . "
                    </select>
                </div>
            </div>
        ";

        $html .= "<div class='mentor-view__mentors'><ul class='mentor-view__list' id='mentor-list'>";
        
        foreach ($this->mentors as $mentor) {
            $html .= $this->getMentorHTML($mentor);
        }

        $html .= '</ul></div></div>';
        return $html;
    }

    private function getMentoringProgramOptionsHTML() {
        $mentoringPrograms = $this->getMentoringPrograms();

        $html = "<option value=''>Chương trình mentoring</option>";

        foreach($mentoringPrograms as $program) {
            $html .= "<option>$program</option>";
        }

        return $html;
    }

    private function getMentoringPrograms() {
        return ["UEH Mentoring", "BK Mentoring", "FTU2 Mentoring", "HN Mentoring"];
    }

    private function getExpertiseOptionsHTML() {
        $expertises = $this->getExpertises();
        sort($expertises);

        $html = "<option value=''>Chuyên ngành</option>";

        foreach($expertises as $expertise) {
            $html .= "<option>$expertise</option>";
        }

        return $html;
    }

    private function getExpertises() {
        $careerFieldExpertiseData = $this->getCareerFieldExpertiseData();
        $expertises = [];

        foreach($careerFieldExpertiseData as $career) {
            foreach($career as $expertise) {
                array_push($expertises, $expertise);
            }
        }

        return $expertises;
    }

    private function getCareerFieldExpertiseData() {
        $json = file_get_contents(DIR_PLUGIN . "/json/career_field_expertise.json");
        $jsonToArray = (array) json_decode($json);
        return $jsonToArray;
      }

    private function getMentorHTML($mentor) {
        return "
        <li class='mentor-view__list-item'>
            <img class='mentor-view__avatar' src='" . $mentor["avatar"] . "' />
            <h5 class='mentor-view__name'>" . $mentor['display_name'] . "</h5>
            <p><strong>" . $mentor['company'] . "</strong></p>
            <p><strong>" . $mentor['title'] . "</strong></p>
            <p class='mentor-view__topics'><strong>Chuyên ngành:</strong> " . $mentor['expertise'] . "<p>
        </li>";
    }
}