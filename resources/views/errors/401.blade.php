@extends('errors::minimal')

@section('title', __('messages.t_unauthorized_title'))
@section('code', '401')
@section('message', __('messages.t_unauthorized_message'))
