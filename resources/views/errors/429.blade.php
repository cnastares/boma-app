@extends('errors::minimal')

@section('title', __('messages.t_too_many_requests_title'))
@section('code', '429')
@section('message', __('messages.t_too_many_requests_message'))
