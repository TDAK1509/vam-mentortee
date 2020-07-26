jQuery(document).ready(function () {
  const mentoringProgramsSelect = jQuery("#mentoring_programs");
  const expertisesSelect = jQuery("#expertises");
  const mentorListDOM = jQuery("#mentor-list");

  mentoringProgramsSelect.on("change", (event) => {
    const selectedProgram = event.target.value;
    const filteredMentors = getMentorsByMentoringProgram(selectedProgram);
    mentorListDOM.html(getMentorListHtml(filteredMentors));
  });

  expertisesSelect.on("change", (event) => {
    const selectedExpertise = event.target.value;
    const filteredMentors = getMentorsByExpertise(selectedExpertise);
    mentorListDOM.html(getMentorListHtml(filteredMentors));
  });
});

function getMentorsByMentoringProgram(mentoringProgram) {
  if (!mentoringProgram) {
    return phpMentors;
  }

  return phpMentors.filter(
    (mentor) => mentor.mentoring_program === mentoringProgram
  );
}

function getMentorsByExpertise(expertise) {
  if (!expertise) {
    return phpMentors;
  }

  return phpMentors.filter((mentor) => mentor.expertise === expertise);
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
