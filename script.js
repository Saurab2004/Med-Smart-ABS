
  document.addEventListener('DOMContentLoaded', () => {
    const dropdown = document.querySelector('.menu li.dropdown');
    const toggle = document.getElementById('dept-toggle');

    toggle.addEventListener('click', (e) => {
      e.preventDefault();
      dropdown.classList.toggle('active');
    });

    // Close dropdown if clicked outside
    document.addEventListener('click', (e) => {
      if (!dropdown.contains(e.target)) {
        dropdown.classList.remove('active');
      }
    });
  });

