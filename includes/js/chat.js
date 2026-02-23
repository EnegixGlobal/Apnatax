$(function() {
    "use strict";

    // Initialize PerfectScrollbar only if elements exist
    const chatBody = document.querySelector('#ChatBody');
    if (chatBody) {
        const ps5 = new PerfectScrollbar('#ChatBody', {
            useBothWheelAxes: true,
            suppressScrollX: true,
        });
    }

    const profileDetails = document.querySelector('.profile-details-main');
    if (profileDetails) {
        const ps6 = new PerfectScrollbar('.profile-details-main', {
            useBothWheelAxes: true,
            suppressScrollX: true,
        });
    }

    const contactsSlider = document.querySelector('.main-chat-contacts-slider');
    if (contactsSlider) {
        const ps7 = new PerfectScrollbar('.main-chat-contacts-slider', {
            useBothWheelAxes: true,
            suppressScrollY: true,
        });
    }

    const mainChat2 = document.querySelector('.main-chat-2');
    if (mainChat2) {
        const ps18 = new PerfectScrollbar('.main-chat-2', {
            useBothWheelAxes: true,
            suppressScrollX: true,
        });
    }
});