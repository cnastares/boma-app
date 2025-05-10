<style>
    .range-slider {
        height: 5px;
        position: relative;
        background-color: #e1e9f6;
        border-radius: 2px;
        /* width: 97%; */
    }

    .range-selected {
        height: 100%;
        position: absolute;
        border-radius: 5px;
        /* background-color: #1b53c0; */
        left: 0;
        right: 0;
        transition: left 0.1s ease, right 0.1s ease;
        /* Smooth transitions */
    }

    .range-input {
        position: relative;
    }

    .range-input input {
        position: absolute;
        width: 103%;
        height: 5px;
        top: -7px;
        background: none;
        pointer-events: none;
        -webkit-appearance: none;
        -moz-appearance: none;
    }

    :root {
        --thumb-border-color: #000000;
    }

    .dark {
        --thumb-border-color: var(--primary);
    }

    .range-input input[type="range"]::-webkit-slider-thumb {
        height: 20px;
        width: 20px;
        border-radius: 50%;
        border: 3px solid var(--thumb-border-color);
        background-color: #fff;
        pointer-events: auto;
        -webkit-appearance: none;
    }

    .range-input input[type="range"]::-moz-range-thumb {
        height: 20px;
        width: 20px;
        border-radius: 50%;
        border: 3px solid var(--thumb-border-color);
        background-color: #fff;
        pointer-events: auto;
    }

    .range-price {
        margin: 10px 0;
        width: 100%;
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .range-price label {
        margin-right: 5px;
    }

    .range-price input {
        width: 100px;
        padding: 5px;
    }

    .range-price input:first-of-type {
        margin-right: 15px;
    }

    /* Hide scrollbar for all browsers */
    .hide-scrollbar {
        overflow: auto;
        /* or overflow: scroll; */
        scrollbar-width: none;
        /* Firefox */
        -ms-overflow-style: none;
        /* Internet Explorer 10+ */
    }

    /* Hide scrollbar for WebKit browsers (Chrome, Safari) */
    .hide-scrollbar::-webkit-scrollbar {
        display: none;
        /* WebKit */
    }
</style>