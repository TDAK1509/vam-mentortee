<?php
if (!defined('ABSPATH')) {
    die;
}

class VamMentorDetails {
    private $enqueueHandleName = "vammentor-details";
    private $userInfo;

    function __construct() {
        $userId = $this->getUserId();
        $this->userInfo = $this->getMentorInfo($userId);
    }

    private function getUserId() {
        return (int) $_GET["id"];
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
        $html = "
        <div class='mentor-details'>
            " . $this->getTopLeftBlockHTML() . "
            " . $this->getBottomLeftBlockHTML() . "
            " . $this->getRightBlockHTML() . "
        </div>
        ";

        return $html;
    }

    private function getMentorInfo($userId) {
        $mentors = $this->getMentors();

        foreach($mentors as $mentor) {
            if ($mentor->ID === $userId) {
                $userMetaData = get_user_meta($mentor->ID);
                $avatar = get_avatar_url($mentor->user_email, ["size" => 215]);

                $userInfo = [
                    "id" => $mentor->ID,
                    "avatar" => $avatar,
                    "name" => $mentor->display_name,
                    "company" => $userMetaData["company"][0],
                    "title" => $userMetaData["title"][0],
                    "email" => $mentor->user_email,
                    "phone" => $userMetaData["phone"][0],
                    "biography" => $userMetaData["description"][0],
                    "year_of_experience" => $userMetaData["year_of_experience"][0],
                    "degree" => $userMetaData["degree"][0],
                    "career_field" => $userMetaData["career_field"][0],
                    "expertise" => $userMetaData["expertise"][0],
                    "topics" => $userMetaData["topics"][0],
                    "hobbies" => $userMetaData["hobbies"][0],
                    "mentoring_program" => $userMetaData["mentoring_program"][0],
                ];

                return (object) $userInfo;
            }
        }

        return [];
    }

    private function getMentors() {
        return get_users([
            'role' => 'mentor',
            'orderby' => 'user_registered',
            'order' => 'ASC'
        ]);
    }

    private function getTopLeftBlockHTML() {
        $avatarUrl = $this->userInfo->avatar;
        $html = 
        "<div class='mentor-details__top-left'>
            <img class='mentor-details__avatar' src='$avatarUrl' alt='Avatar' />
        </div>";
        return $html;
    }


    private function getBottomLeftBlockHTML() {
        $userInfo = $this->userInfo;
        $emailPhoneHtml = "";
        

        if ($this->userIsAdmin()) {
            $dividerHtml = "<div class='mentor-details__line'></div>";
            $emailHtml = $this->getContactInfoRow($this->getEmailIcon(), $userInfo->email);
            $phoneHtml = $this->getContactInfoRow($this->getPhoneIcon(), $userInfo->phone);
            $emailPhoneHtml = $dividerHtml . $emailHtml . $phoneHtml;
        }

        $html = 
        "<div class='mentor-details__bottom-left'>
            <h5 class='mentor-details__heading'>$userInfo->name</h5>
            <p>$userInfo->title</p>
            <p><strong>$userInfo->company</strong></p>
            $emailPhoneHtml
        </div>";
        return $html;
    }

    private function userIsAdmin() {
        return current_user_can('administrator');
    }

    private function getContactInfoRow($icon, $value) {
        return 
        "<p class='mentor-details__contact-info'>
            {$icon}
            <span>: {$value}</span>
        </p>";
    }    

    private function getEmailIcon() {
        $iconUrl = DIR_PLUGIN . "/assets/svg/email.svg";
        return "<img src='$iconUrl' class='mentor-details__icon' />";
    }

    private function getPhoneIcon() {
        $iconUrl = DIR_PLUGIN . "/assets/svg/mobile.svg";
        return "<img src='$iconUrl' class='mentor-details__icon' />";
    }

    private function getRightBlockHTML() {
        $userInfo = $this->userInfo;

        $html = 
        "<div class='mentor-details__right'>
            " . $this->getRightBlockRowHTML("Tiểu sử", $userInfo->biography) . "
            " . $this->getRightBlockRowHTML("Số năm kinh nghiệm", $userInfo->year_of_experience) . "
            " . $this->getRightBlockRowHTML("Trình độ học vấn", $userInfo->degree) . "
            " . $this->getRightBlockRowHTML("Lĩnh vực nghề nghiệp", $userInfo->career_field) . "
            " . $this->getRightBlockRowHTML("Chuyên môn", $userInfo->expertise) . "
            " . $this->getRightBlockRowHTML("Chủ đề chia sẻ", $userInfo->topics) . "
            " . $this->getRightBlockRowHTML("Sở thích", $userInfo->hobbies) . "
            " . $this->getRightBlockRowHTML("Chương trình mentoring", $userInfo->mentoring_program) . "
        </div>";

        return $html;
    }

    private function getRightBlockRowHTML($title, $content) {
        $html =
        "<div class='mentor-details__right-row'>
            <p class='mentor-details__right-title'>$title</p>
            <p>$content</p>
        </div>";

        return $html;
    }
}