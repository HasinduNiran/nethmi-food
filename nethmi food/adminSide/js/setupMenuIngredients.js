// Initialize the page
document.addEventListener("DOMContentLoaded", function () {
  // Enhanced validation for the form
  document
    .getElementById("addIngredientForm")
    .addEventListener("submit", function (event) {
      let isValid = true;

      // Check ingredient selection
      const ingredientSelect = this.querySelector(
        'select[name="ingredient_id"]'
      );
      if (!ingredientSelect.value) {
        ingredientSelect.classList.add("is-invalid");
        isValid = false;
      } else {
        ingredientSelect.classList.remove("is-invalid");
      }

      // Check quantity
      const quantityInput = this.querySelector('input[name="quantity"]');
      if (!quantityInput.value || parseFloat(quantityInput.value) <= 0) {
        quantityInput.classList.add("is-invalid");
        isValid = false;
      } else {
        quantityInput.classList.remove("is-invalid");
      }

      // Check measurement
      const measurementSelect = this.querySelector(
        'select[name="measurement"]'
      );
      if (!measurementSelect.value) {
        measurementSelect.classList.add("is-invalid");
        isValid = false;
      } else {
        measurementSelect.classList.remove("is-invalid");
      }

      if (!isValid) {
        event.preventDefault();

        // Show alert if form is invalid
        if (!document.querySelector(".validation-alert")) {
          const alertDiv = document.createElement("div");
          alertDiv.className = "alert alert-danger validation-alert mt-3";
          alertDiv.innerHTML = "Please fill out all required fields correctly.";
          document.getElementById("addIngredientForm").prepend(alertDiv);

          // Auto-dismiss the alert after 3 seconds
          setTimeout(function () {
            alertDiv.remove();
          }, 3000);
        }
      }
    });

  // Add event listeners for all inputs to remove is-invalid class when user types
  document
    .querySelectorAll("#addIngredientForm input, #addIngredientForm select")
    .forEach(function (element) {
      element.addEventListener("change", function () {
        this.classList.remove("is-invalid");
      });
    });

  // Enhance the mobile experience with responsive tweaks
  if (window.innerWidth < 768) {
    document.querySelectorAll(".form-row").forEach((row) => {
      row.classList.remove("form-row");
    });
  }
});

// Function to add deletion confirmation
function confirmDelete(event, id) {
  if (!confirm("Are you sure you want to delete this ingredient?")) {
    event.preventDefault();
  }
}
