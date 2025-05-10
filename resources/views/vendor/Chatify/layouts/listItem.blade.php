
{{-- -------------------- Contact list -------------------- --}}
@if($get == 'users' && !!$lastMessage)
<?php
$lastMessageBody = mb_convert_encoding($lastMessage->content, 'UTF-8', 'UTF-8');
$lastMessageBody = strlen($lastMessageBody) > 30 ? mb_substr($lastMessageBody, 0, 30, 'UTF-8').'..' : $lastMessageBody;
?>
<table  class="messenger-list-item border-spacing-y-5  hover:bg-[#e9eef5] dark:hover:bg-[#323232]" data-contact="{{ $contact['conversation_id'] }}">
    <tr data-action="0" tabindex="0" class="focus-within:outline focus-within:outline-inset focus-within:outline-primary-600">

        {{-- Avatar side --}}
        <td style="position: relative">
            @if($contact['active_status'])
                <span class="activeStatus"></span>
            @endif
            <div class="avatar av-m" style="background-image: url('{{ $contact['ad_image'] }}');"></div>
        </td>

        {{-- center side --}}
        <td>
            <p data-id="{{  $contact['conversation_id'] }}" data-type="user" class="!text-[13px] !font-medium text-slate-700 rtl:flex rtl:justify-between">

                {{-- Username --}}
                {{ strlen($contact['name']) > 15 ? trim(substr($contact['name'], 0, 15)).'...' : $contact['name'] }}


                {{-- Last message date --}}
                <span class="!text-xs font-normal !text-slate-600 contact-item-time" data-time="{{$lastMessage->created_at}}">{{ format_date($lastMessage->created_at) }}</span>

            </p>
            <p class="text-base line-clamp-1">
                {{ $contact['ad_title'] }}
            </p>

            <span class="!text-xs !text-slate-500 flex items-center justify-between h-5">

                {{-- Last message --}}

                <div class="rtl:flex flex" dir="{{ config()->get('direction') }}">
                    {{-- Last Message user indicator --}}
                    {!!
                        $lastMessage->sender_id == Auth::user()->id
                        ? '<span class="lastMessageIndicator !text-xs !font-medium ltr:pr-1 rtl:pl-1">' . __("messages.t_you") . '</span>'
                        : ''
                    !!}
                    {{-- Last message body --}}
                    @if($lastMessage->attachment == null)
                        {!!
                            strlen($lastMessage->content) > 30
                            ? trim(substr($lastMessage->content, 0, 30)).'..'
                            : $lastMessage->content
                        !!}
                    @else

                        @php
                            $msg_attach = json_decode($lastMessage->attachment);
                        @endphp

                        {{-- Image/File --}}
                        @if (in_array($msg_attach->extension, explode(',', $liveChatSettings->allowed_image_extensions)))
                            <div class="flex items-center space-x-1 rtl:space-x-reverse ltr:ml-1.5 rtl:mr-1.5">
                                <svg class="h-4 w-4 dark:text-gray-300" stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 20 20" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path></svg>
                                <div>@lang('messages.t_image')</div>
                            </div>
                        @else
                            <div class="flex items-center space-x-1 rtl:space-x-reverse ltr:ml-1.5 rtl:mr-1.5">
                                <svg class="h-4 w-4 dark:text-gray-300" stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 20 20" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path></svg>
                                <div>@lang('messages.t_attachment')</div>
                            </div>
                        @endif

                    @endif
                </div>

                {{-- New messages counter --}}
                {!! $unseenCounter > 0 ? "<b class='new-messages-counter'>".$unseenCounter."</b>" : '' !!}

            </span>

        </td>

    </tr>
</table>
<livewire:layout.bottom-navigation />
@endif

{{-- -------------------- Shared photos Item -------------------- --}}
@if($get == 'sharedPhoto')
<div class="shared-photo chat-image" style="background-image: url('{{ $image }}')"></div>
@endif


