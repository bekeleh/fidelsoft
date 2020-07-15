@extends('layouts.master')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-9 col-md-push-3">
            @if ( Auth::guest() AND !app('request')->input('page') )
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div id="questions">
                                <legend class="text-left">Knowledge Base</legend>
                            </div>
                            <P>Knowledge base in the tech industry can be very challenging especially when it comes to
                                programming. Improving your interviewing communication skills can land you a higher
                            paying job with a better company.</P>
                            <P>This site was created to improve your interviewing knowledge based upon your programming
                            skills.</P>
                            <P>This site will help you demonstrate basic and advanced knowledge in your skillset.</P>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <p> FidelERP Bussiness Management Suit </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-md-pull-9">
         <p>if any side bar</p>
     </div>
 </div>
</div>
@endsection