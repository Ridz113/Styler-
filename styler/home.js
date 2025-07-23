
function toggleDropdown(menuId) {
    console.log("Menu Clicked");
    // Close all open menus
    const allMenus = document.querySelectorAll(".filter-menu.-active");
    allMenus.forEach((menu) => {
        if (menu.id !== menuId) {
            menu.classList.remove("-active", "-show");
        }
    });

    // Find the clicked menu
    const menu = document.getElementById(menuId);
    if (!menu) {
        console.error(`Menu with ID "${menuId}" not found`);
        return;
    }

    // Toggle the clicked menu
    if (menu.classList.contains("-active")) {
        menu.classList.remove("-active", "-show");
    } else {
        menu.classList.add("-active", "-show");
    }
}

// Budget slider code
const minRange = document.getElementById('min-range');
const maxRange = document.getElementById('max-range');
const minValue = document.getElementById('min-value');
const maxValue = document.getElementById('max-value');

minRange.addEventListener('input', () => {
    if (parseInt(minRange.value) > parseInt(maxRange.value) - 5) {
        minRange.value = parseInt(maxRange.value) - 5;
    }
    minValue.textContent = `£${minRange.value}`;
});

maxRange.addEventListener('input', () => {
    if (parseInt(maxRange.value) < parseInt(minRange.value) + 5) {
        maxRange.value = parseInt(minRange.value) + 5;
    }
    maxValue.textContent = `£${maxRange.value}`;
});