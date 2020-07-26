let selectedProgram = "";
let selectedExpertise = "";

jQuery(document).ready(function () {
  const mentoringProgramsSelect = jQuery("#mentoring_programs");
  const expertisesSelect = jQuery("#expertises");
  const mentorListDOM = jQuery("#mentor-list");

  mentoringProgramsSelect.on("change", (event) => {
    selectedProgram = event.target.value;
    const filteredMentors = filterMentors();
    mentorListDOM.html(getMentorListHtml(filteredMentors));
  });

  expertisesSelect.on("change", (event) => {
    selectedExpertise = event.target.value;
    const filteredMentors = filterMentors();
    mentorListDOM.html(getMentorListHtml(filteredMentors));
  });
});

function filterMentors() {
  let filteredMentors = phpMentors;

  if (selectedProgram) {
    filteredMentors = filteredMentors.filter(
      (mentor) => mentor.mentoring_program === selectedProgram
    );
  }

  if (selectedExpertise) {
    filteredMentors = filteredMentors.filter(
      (mentor) => mentor.expertise === selectedExpertise
    );
  }

  return filteredMentors;
}

function getMentorListHtml(mentors) {
  let html = "";

  mentors.forEach((mentor) => {
    html += getMentorHtml(mentor);
  });

  return html;
}

function getMentorHtml(mentor) {
  return `<li class='mentor-view__list-item'>
    <img class='mentor-view__avatar' src='${mentor.avatar}' />
    <h5 class='mentor-view__name'>${mentor.display_name}</h5>
    <p><strong>${mentor.company}</strong></p>
    <p><strong>${mentor.title}</strong></p>
    <p class='mentor-view__topics'><strong>Chuyên ngành:</strong> ${mentor.expertise}<p>
  </li>`;
}
