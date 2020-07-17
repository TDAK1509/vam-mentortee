jQuery(document).ready(function () {
  const careerField = jQuery("#career_field");
  const expertiseField = jQuery("#expertise");

  careerField.on("change", function () {
    const selectedCareer = careerField.val();
    const expertises = phpCareerExpertiseObj[selectedCareer];
    expertiseField.html(generateExpertiseOptions(expertises));
  });
});

function generateExpertiseOptions(expertises) {
  const expertiseHtml = expertises.map(
    (expertise) => `<option>${expertise}</option>`
  );
  expertiseHtml.unshift("<option value=''>Please select expertise</option>");
  return expertiseHtml;
}
