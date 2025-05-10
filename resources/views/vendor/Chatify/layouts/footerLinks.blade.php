<script src="https://js.pusher.com/7.2.0/pusher.min.js"></script>
<script>

    // Gloabl Chatify variables from PHP to JS
    window.chatify = {
        enable_attachments       : Boolean({{ $liveChatSettings->enable_uploading_attachments }}),
        enable_emojis            : Boolean({{ $liveChatSettings->enable_emojis }}),
        enable_notification_sound: Boolean({{ $liveChatSettings->play_new_message_sound }}),
        notification_sound       : "{{ url('public/js/chatify/sounds/new-message-sound.mp3') }}"
    };

    // Disable pusher logging
    Pusher.logToConsole = false;

    var pusher = new Pusher("{{ config('chatify.pusher.key') }}", {
        encrypted   : Boolean({{ config('chatify.pusher.options.encrypted') ? 1 : 0 }}),
        cluster     : "{{ config('chatify.pusher.options.cluster') }}",
        authEndpoint: '{{ route("pusher.auth") }}',
        auth        : {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        }
    });

    // Bellow are all the methods/variables that using php to assign globally.
    const allowedImages        = {!! json_encode( explode(',', $liveChatSettings->allowed_image_extensions) ) !!} || [];
    const allowedFiles         = {!! json_encode( explode(',', $liveChatSettings->allowed_file_extensions) ) !!} || [];
    const getAllowedExtensions = [...allowedImages, ...allowedFiles];
    const getMaxUploadSize     = {{ $liveChatSettings->max_file_size * 1048576 }};
   // Wait for the DOM to be fully loaded
   document.addEventListener('DOMContentLoaded', (event) => {
        // Find the file input and set its 'accept' attribute
        const fileInput = document.querySelector('.upload-attachment');
        if (fileInput) {
            fileInput.setAttribute('accept', getAllowedExtensions.join(', .'));
        }
    });
</script>
 <!-- Insert Custom Script in Body -->
 {!! $scriptSettings->custom_script_body !!}
<script src="{{ asset('js/chatify/utils.js') }}"></script>
<script src="{{ asset('js/chatify/code.js') }}"></script>
