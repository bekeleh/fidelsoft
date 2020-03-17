@extends('errors::layout')

@section('title', 'Error')

@section('message', 'Something went wrong. Check your account authorized to do such thing, or contact your IT admin, Maybe <a href="{{ url('/') }}">')
