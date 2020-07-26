@extends('header')

@section('onReady')
    $('input#name').focus();
@stop
@section('head')
    @if (config('ninja.google_maps_api_key'))
        @include('partials.google_geocode')
    @endif
@stop
@section('content')
    @if ($errors->first('contacts'))
        <div class="alert alert-danger">{{ trans($errors->first('contacts')) }}</div>
    @endif
    <div class="row">
        {!! Former::open($url)
        ->autocomplete('off')
        ->rules([
        'id_number' => 'required',
        'name' => 'required',
        'email' => 'required|email',
        'work_phone' => 'required',
        'currency_id' => 'required'
        ])->addClass('col-md-12 warn-on-exit')
        ->method($method) !!}

        @include('partials.autocomplete_fix')
        @if ($vendor)
            {!! Former::populate($vendor) !!}
            {!! Former::hidden('public_id') !!}
        @endif
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading" style="color:white;background-color: #777 !important;">
                        <h3 class="panel-title in-bold-white">{!! trans('texts.organization') !!}</h3>
                    </div>
                    <div class="panel-body">
                        {!! Former::text('name')->label('vendor_name')->data_bind("attr { placeholder: placeholderName }") !!}
                        {!! Former::text('id_number') !!}
                        {!! Former::text('vat_number') !!}
                        {!! Former::text('website') !!}
                        {!! Former::text('work_phone') !!}
                        @include('partials/custom_fields', ['entityType' => ENTITY_VENDOR])
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading" style="color:white;background-color: #777 !important;">
                        <h3 class="panel-title in-bold-white">{!! trans('texts.address') !!}</h3>
                    </div>
                    <div class="panel-body" id="billing_address">
                        {!! Former::text('address1') !!}
                        {!! Former::text('address2') !!}
                        {!! Former::text('city') !!}
                        {!! Former::text('state') !!}
                        {!! Former::text('postal_code')
                        ->oninput(config('ninja.google_maps_api_key') ? 'lookupPostalCode()' : '') !!}
                        {!! Former::select('country_id')->addOption('','')
                        ->autocomplete('off')
                        ->fromQuery($countries, 'name', 'id') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading" style="color:white;background-color: #777 !important;">
                        <h3 class="panel-title in-bold-white">{!! trans('texts.contacts') !!}</h3>
                    </div>
                    <div class="panel-body">
                        <div data-bind='template: {
                            foreach: contacts,
                            beforeRemove: hideContact,
                            afterAdd: showContact }'>
                            {!! Former::hidden('public_id')->data_bind("value: public_id, valueUpdate: 'afterkeydown',
                            attr: {name: 'contacts[' + \$index() + '][public_id]'}") !!}
                            {!! Former::text('first_name')->data_bind("value: first_name, valueUpdate: 'afterkeydown',
                            attr: {name: 'contacts[' + \$index() + '][first_name]'}") !!}
                            {!! Former::text('last_name')->data_bind("value: last_name, valueUpdate: 'afterkeydown',
                            attr: {name: 'contacts[' + \$index() + '][last_name]'}") !!}
                            {!! Former::text('email')->data_bind("value: email, valueUpdate: 'afterkeydown',
                            attr: {name: 'contacts[' + \$index() + '][email]', id:'email'+\$index()}") !!}
                            {!! Former::text('phone')->data_bind("value: phone, valueUpdate: 'afterkeydown',
                            attr: {name: 'contacts[' + \$index() + '][phone]'}") !!}
                            <div class="form-group">
                                <div class="col-lg-8 col-lg-offset-4 bold">
                                    <span class="redlink bold"
                                          data-bind="visible: $parent.contacts().length > 1">
                                    {!! link_to('#', trans('texts.remove_contact').' -', array('data-bind'=>'click: $parent.removeContact')) !!}
                                    </span>
                                    <span data-bind="visible: $index() === ($parent.contacts().length - 1)"
                                          class="pull-right greenlink bold">
                                    {!! link_to('#', trans('texts.add_contact').' +', array('onclick'=>'return addContact()')) !!}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading" style="color:white;background-color: #777 !important;">
                        <h3 class="panel-title in-bold-white">{!! trans('texts.additional_info') !!}</h3>
                    </div>
                    <div class="panel-body">
                        {!! Former::select('currency_id')->addOption('','')
                        ->placeholder($account->currency ? $account->currency->name : '')
                        ->fromQuery($currencies, 'name', 'id') !!}
                        {!! Former::textarea('public_notes')->rows(6) !!}
                        {!! Former::textarea('private_notes')->rows(6) !!}
                    </div>
                </div>

            </div>
        </div>
        {!! Former::hidden('data')->data_bind("value: ko.toJSON(model)") !!}
        <script type="text/javascript">
            $(function () {
                $('#country_id').combobox();
            });

            function VendorContactModel(data) {
                var self = this;
                self.public_id = ko.observable('');
                self.first_name = ko.observable('');
                self.last_name = ko.observable('');
                self.email = ko.observable('');
                self.phone = ko.observable('');
                self.password = ko.observable('');
                self.custom_value1 = ko.observable('');
                self.custom_value2 = ko.observable('');
                if (data) {
                    ko.mapping.fromJS(data, {}, this);
                }
            }

            function VendorModel(data) {
                var self = this;
                self.contacts = ko.observableArray();

                self.mapping = {
                    'contacts': {
                        create: function (options) {
                            return new VendorContactModel(options.data);
                        }
                    }
                };
                if (data) {
                    ko.mapping.fromJS(data, self.mapping, this);
                } else {
                    self.contacts.push(new VendorContactModel());
                }

                self.placeholderName = ko.computed(function () {
                    if (self.contacts().length == 0) return '';
                    var contact = self.contacts()[0];
                    if (contact.first_name() || contact.last_name()) {
                        return (contact.first_name() || '') + ' ' + (contact.last_name() || '');
                    } else {
                        return contact.email();
                    }
                });
            }

            @if ($data)
                window.model = new VendorModel({!! $data !!});
            @else
                window.model = new VendorModel({!! $vendor !!});
            @endif

                model.showContact = function (elem) {
                if (elem.nodeType === 1) $(elem).hide().slideDown()
            };
            model.hideContact = function (elem) {
                if (elem.nodeType === 1) $(elem).slideUp(function () {
                    $(elem).remove();
                })
            };
            ko.applyBindings(model);

            function addContact() {
                model.contacts.push(new VendorContactModel());
                return false;
            }

            model.removeContact = function () {
                model.contacts.remove(this);
            }

        </script>
        @if(Auth::user()->canCreateOrEdit(ENTITY_VENDOR))
            <center class="buttons">
                {!! Button::normal(trans('texts.cancel'))->large()->asLinkTo(URL::to('/vendors/' . ($vendor ? $vendor->public_id : '')))->appendIcon(Icon::create('remove-circle')) !!}
                {!! Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')) !!}
            </center>
        @endif
        {!! Former::close() !!}
    </div>
@stop
