@if ($externalAdSettings->enable && (!getSubscriptionSetting('status') || (getSubscriptionSetting('status') && in_array(getUserSubscriptionPlan($ad->user_id)?->ads_level, ['basic', 'advanced']))))
<style>
    .external-ad {
        padding-top: @php echo ($externalAdSettings->ad_top_spacing ?? 8) . 'px';
        @endphp ;
        padding-right: @php echo ($externalAdSettings->ad_right_spacing ?? 8) . 'px';
        @endphp ;
        padding-bottom: @php echo ($externalAdSettings->ad_bottom_spacing ?? 8) . 'px';
        @endphp ;
        padding-left: @php echo ($externalAdSettings->ad_left_spacing ?? 8) . 'px';
        @endphp ;
    }
</style>

<!-- external ads -->
<div class="external-ad w-full border-b classic:border-black ">
    {!! $externalAdSettings->value !!}
</div>
@endif
