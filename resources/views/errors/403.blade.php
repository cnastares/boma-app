@extends('errors::minimal')

@section('title', __('messages.t_forbidden_title'))
@section('code', '403')
@section('message', __($exception->getMessage() ?: __('messages.t_forbidden_message')))
