{{-- ---------------------- Image modal box ---------------------- --}}
<div id="imageModalBox" class="imageModal">
    <span class="bg-zinc-700 flex h-10 imageModal-close items-center justify-center rounded-full text-2xl text-white w-10">
        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
            <g>
                <path fill="none" d="M0 0h24v24H0z"></path>
                <path d="M12 10.586l4.95-4.95 1.414 1.414-4.95 4.95 4.95 4.95-1.414 1.414-4.95-4.95-4.95 4.95-1.414-1.414 4.95-4.95-4.95-4.95L7.05 5.636z"></path>
            </g>
        </svg>
    </span>
    <img class="imageModal-content" id="imageModalBoxSrc">
</div>


{{-- ---------------------- Delete Modal ---------------------- --}}
<div class="app-modal" data-name="delete">
    <div class="app-modal-container">
        <div class="app-modal-card" data-name="delete" data-modal='0'>
            <div class="app-modal-header">{{ __("messages.t_are_you_sure_you_want_to_delete_this") }}</div>
            <div class="app-modal-body">{{ __("messages.t_you_can_not_undo_this_action") }}</div>
            <div class="app-modal-footer">
                <a href="javascript:void(0)" class="app-btn cancel">{{ __("messages.t_cancel") }}</a>
                <a href="javascript:void(0)" class="app-btn a-btn-danger delete">{{ __("messages.t_delete") }}</a>
            </div>
        </div>
    </div>
</div>
{{-- ---------------------- Alert Modal ---------------------- --}}
<div class="app-modal" data-name="alert">
    <div class="app-modal-container">
        <div class="app-modal-card" data-name="alert" data-modal='0'>
            <div class="app-modal-header"></div>
            <div class="app-modal-body"></div>
            <div class="app-modal-footer">
                <a href="javascript:void(0)" class="app-btn cancel">{{ __("messages.t_cancel") }}</a>
            </div>
        </div>
    </div>
</div>