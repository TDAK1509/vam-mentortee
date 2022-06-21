let selectedProgram = "";
let selectedExpertise = "";

jQuery(document).ready(function () {
  const mentoringProgramsSelect = jQuery("#mentoring_programs");
  const expertisesSelect = jQuery("#expertises");
  const mentorListDOM = jQuery("#mentor-list");

  mentoringProgramsSelect.on("change", event => {
    selectedProgram = event.target.value;
    const filteredMentors = filterMentors();
    mentorListDOM.html(getMentorListHtml(filteredMentors));
  });

  expertisesSelect.on("change", event => {
    selectedExpertise = event.target.value;
    const filteredMentors = filterMentors();
    mentorListDOM.html(getMentorListHtml(filteredMentors));
  });
});

function filterMentors() {
  let filteredMentors = phpMentors;

  if (selectedProgram) {
    filteredMentors = filteredMentors.filter(mentor => {
      return mentor.mentoring_program
        ? mentor.mentoring_program.includes(selectedProgram)
        : false;
    });
  }

  if (selectedExpertise) {
    filteredMentors = filteredMentors.filter(
      mentor => mentor.expertise === selectedExpertise
    );
  }

  return filteredMentors;
}

function getMentorListHtml(mentors) {
  let html = "";

  mentors.forEach(mentor => {
    html += getMentorHtml(mentor);
  });

  return html;
}

function getMentorHtml(mentor) {
  const mentorDetailsPageUrl = window.location.href.replace(
    "/mentor-list",
    "/mentor-details"
  );
  return `<li class='mentor-view__list-item'>
  <a href='${mentorDetailsPageUrl}?id=${mentor.id}'>
      <img class='mentor-list-item__avatar' src='${mentor.avatar}' />
  </a>
  <p class='mentor-list-item__name'>${mentor.display_name}</p>
  <p class='mentor-list-item__subtitle'>${mentor.title}</p>
  <p class='mentor-list-item__subtitle'>${mentor.company}</p>
  <div class='mentor-list-item__divider'></div>
  <p class='mentor-list-item__topics'><strong>Chuyên ngành:</strong> ${mentor.expertise}<p>
</li>`;
}
