@extends('errors::minimal')

@section('title', __('messages.t_server_error_title'))
@section('code', '500')
@section('message', __('messages.t_server_error_message'))
