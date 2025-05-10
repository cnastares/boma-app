@extends('errors::minimal')

@section('title', config('maintenance.headline'))
@section('code', '503')
@section('message', config('maintenance.message'))
