document.addEventListener('DOMContentLoaded', function() {
    const dropdown = document.querySelector('.dropdown');
    const button = dropdown.querySelector('.link');
    const dropdownContent = dropdown.querySelector('.dropdown-content');

    button.addEventListener('mouseenter', () => {
        dropdown.classList.add('active');
    });

    dropdown.addEventListener('mouseleave', () => {
        dropdown.classList.remove('active');
    });
});