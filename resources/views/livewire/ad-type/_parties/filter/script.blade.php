@if ($adType?->filter_settings['enable_price_range_toggle'])
@script
<script>
    const range = document.querySelector(".range-selected");

    const rangeInput = document.querySelectorAll(".range-input input");
    const rangePrice = document.querySelectorAll(".range-price input");

    const minRangeGap = 10; // Minimum gap between the two ranges

    function updateRange() {
        const minValue = parseInt(rangeInput[0].value);
        const maxValue = parseInt(rangeInput[1].value);
        // console.log(maxValue);
        // Ensure minimum gap
        if (maxValue - minValue < minRangeGap) {
            if (event.target.classList.contains("min")) {
                rangeInput[0].value = maxValue - minRangeGap;
            } else {
                rangeInput[1].value = minValue + minRangeGap;
            }
        }

        // Update price input fields
        rangePrice[0].value = rangeInput[0].value;
        rangePrice[1].value = rangeInput[1].value;

        // Calculate percentages for styling
        const minPercentage = ((minValue - rangeInput[0].min) / (rangeInput[0].max - rangeInput[0].min)) * 100;
        const maxPercentage = ((maxValue - rangeInput[1].min) / (rangeInput[1].max - rangeInput[1].min)) * 100;

        // Update the selected range style
        range.style.left = `${minPercentage}%`;
        range.style.right = `${100 - maxPercentage}%`;
    }

    // Add event listeners to the range inputs
    rangeInput.forEach(input => {
        input.addEventListener("input", updateRange);
    });

    // Add event listeners for the price inputs
    rangePrice.forEach((input, index) => {
        input.addEventListener("input", () => {
            const minPrice = parseInt(rangePrice[0].value);
            const maxPrice = parseInt(rangePrice[1].value);

            if (maxPrice - minPrice >= minRangeGap && maxPrice <= rangeInput[1].max && minPrice >=
                rangeInput[0].min) {
                if (index === 0) {
                    rangeInput[0].value = minPrice;
                } else {
                    rangeInput[1].value = maxPrice;
                }
                updateRange();
            }
        });
    });

    $wire.on('update-range', () => {
        const range = document.querySelector(".range-selected");
        const rangeInput = document.querySelectorAll(".range-input input");
        const rangePrice = document.querySelectorAll(".range-price input");

        if (!range || rangeInput.length < 2 || rangePrice.length < 2) {
            console.error("Elements not found in the DOM.");
            return;
        }

        const minValue = parseInt(rangeInput[0].value);
        const maxValue = parseInt(rangeInput[1].value);

        if (maxValue - minValue < minRangeGap) {
            if (event.target.classList.contains("min")) {
                rangeInput[0].value = maxValue - minRangeGap;
            } else {
                rangeInput[1].value = minValue + minRangeGap;
            }
        }

        rangePrice[0].value = rangeInput[0].value;
        rangePrice[1].value = rangeInput[1].value;

        const minPercentage = ((minValue - rangeInput[0].min) / (rangeInput[0].max - rangeInput[0].min)) * 100;
        const maxPercentage = ((maxValue - rangeInput[1].min) / (rangeInput[1].max - rangeInput[1].min)) * 100;

        // Apply styles with !important
        setTimeout(() => {
            range.style.setProperty('left', `${minPercentage}%`, 'important');
            range.style.setProperty('right', `${100 - maxPercentage}%`, 'important');
        }, 10);

        // console.log(range, "Range styles updated:", range.style.left, range.style.right);
    });

    // Initialize the range on page load
    updateRange();
</script>
@endscript
@endif