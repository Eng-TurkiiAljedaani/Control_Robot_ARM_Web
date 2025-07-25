document.addEventListener("DOMContentLoaded", function () {
  const resetButton = document.querySelector('button[type="reset"]');
  if (resetButton) {
    resetButton.addEventListener("click", function () {
      setTimeout(() => {
        for (let i = 1; i <= 6; i++) {
          const slider = document.getElementById(`joint_${i}`);
          const label = document.getElementById(`val_${i}`);
          label.innerText = slider.value;
        }
      }, 50);
    });
  }
});
