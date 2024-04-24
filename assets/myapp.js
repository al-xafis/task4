let mainCheckbox = document.querySelector(".mainCheckbox");
let subCheckboxes = document.querySelectorAll(".subCheckbox");

function handleMainCheckboxChange() {
  const isChecked = mainCheckbox.checked;
  // Set all sub-checkboxes to the state of the main checkbox
  for (let i = 0; i < subCheckboxes.length; i++) {
    subCheckboxes[i].checked = isChecked;
  }
}

function handleSubCheckboxChange() {
  let allChecked = true;
  // Check if all sub-checkboxes are checked
  for (let i = 0; i < subCheckboxes.length; i++) {
    if (!subCheckboxes[i].checked) {
      allChecked = false;
      break;
    }
  }
  // Set main checkbox state based on sub-checkboxes
  mainCheckbox.checked = allChecked;
}

mainCheckbox.addEventListener("change", handleMainCheckboxChange);
for (let i = 0; i < subCheckboxes.length; i++) {
  subCheckboxes[i].addEventListener("change", handleSubCheckboxChange);
}
