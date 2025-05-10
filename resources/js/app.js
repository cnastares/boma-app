import './bootstrap';

import Alpine from 'alpinejs';
import persist from '@alpinejs/persist';

Alpine.plugin(persist);

window.Alpine = Alpine;

Alpine.start();
// // Select all buttons with the class 'fi-modal-close-btn'
// const closeButtons = document.querySelectorAll('.fi-modal-close-btn');

// // Set tabindex to 0 for each button
// closeButtons.forEach((button) => {
//     button.setAttribute('tabindex', '0');
// });
