// Toggle active link based on current path
const path = location.pathname.split('/').pop();

// Select all dashboard menu links
document.querySelectorAll('.dashboard__menu-link').forEach(link => {
    const href = link.getAttribute('href');

    // Normal active check
    if (href && href.endsWith(path)) {
        link.classList.add('active');
    }

    // Special case: "Add Listing" parent link
    const addListingLink = document.querySelector('a.dashboard__menu-link[href="profile-add-listing.html"]');
    const addListingDropdownPages = [
        'profile-add-listing-car-services.html',
        'profile-add-listing-car-repair.html',
        'profile-add-listing-general-car-service.html'

    ];

    if (addListingLink && addListingDropdownPages.includes(path)) {
        addListingLink.classList.add('active');
    }
});


// Dropdown navigation inside Add Listing
const internalDropdowns = document.querySelectorAll('[data-internal-dropdown]');
internalDropdowns.forEach(dd => {
    dd.addEventListener('click', (e) => {
        e.preventDefault();
        dd.classList.toggle('show');
        const menu = dd.nextElementSibling;
        if (menu) menu.classList.toggle('show');
    });
});

// Dashboard User Avatar File
$(".dashboard__user-avatar-btn").click(function () {
    $(".dashboard__user-avatar-file").click();
});

// Choose File
$(".dashboard__add-listing-details-upload-btn").click(function () {
    $(".dashboard__add-listing-details-upload-file").click();
});


// Dashboard User Dropdown 
const userImage = document.querySelector('.dashboard__user-image');
const userMenu = document.querySelector('.dashboard__user-menu');

userImage.addEventListener('click', () => {
    userMenu.classList.toggle('show');
});

document.addEventListener('click', (e) => {
    if (!userImage.contains(e.target) && !userMenu.contains(e.target)) {
        userMenu.classList.remove('show');
    }
});