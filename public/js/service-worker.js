document.addEventListener('DOMContentLoaded', () => {
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js')
            .then((registration) => console.log('ServiceWorker registered:', registration.scope))
            .catch((err) => console.log('ServiceWorker registration failed:', err));
    }
});

// document.querySelectorAll("[class*='bg-primary'], [class*='bg-price-gradient']").forEach(btn => {
//     let bgColor;

//     if (isGradientBackground(btn)) {

//         bgColor = getBackgroundColorFromGradient(btn); // Extract color from gradient
//     } else {
//         bgColor = window.getComputedStyle(btn).backgroundColor; // Extract normal background color
//     }

//     if (bgColor) {
//         const contrastColor = getContrastColor(bgColor);
//         btn.style.setProperty("color", contrastColor, "important"); // Override Tailwind
//         console.log(`Applied ${contrastColor} for ${bgColor}`, btn);
//     }
// });

// // **Check if an element has a gradient background**
// function isGradientBackground(element) {
//     const bgImage = window.getComputedStyle(element).backgroundImage;

//     console.log(element,bgImage,bgImage.includes("gradient"));
//     return bgImage.includes("gradient");
// }

// // **Extract the dominant color from a gradient background**
// function getBackgroundColorFromGradient(element) {
//     const canvas = document.createElement("canvas");
//     const ctx = canvas.getContext("2d");
//     canvas.width = 1;
//     canvas.height = 1;

//     // Apply computed background style
//     const style = window.getComputedStyle(element);
//     ctx.fillStyle = style.backgroundImage;

//     // Draw a small 1px box to get the color
//     ctx.fillRect(0, 0, 1, 1);

//     // Read the pixel color
//     const pixel = ctx.getImageData(0, 0, 1, 1).data;
//     return `rgb(${pixel[0]}, ${pixel[1]}, ${pixel[2]})`;
// }

// // **Determine contrast color (black or white)**
// function getContrastColor(bgColor) {
//     const rgb = bgColor.match(/\d+/g).map(Number);
//     const brightness = (rgb[0] * 299 + rgb[1] * 587 + rgb[2] * 114) / 1000;
//     return brightness > 128 ? "black" : "white";
// }

