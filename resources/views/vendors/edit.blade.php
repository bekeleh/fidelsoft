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
            'email' => 'required|email',
            'work_phone'=>'required',
            'id_number'=>'required',
            'vendor_type_id'=>'required',
            'hold_reason_id'=>'required',
            'country_id'=>'required',
            'language_id'=>'required',
            'currency_id'=>'required',
            'payment_terms'=>'required'
            ])->addClass('col-md-12 warn-on-exit')
            ->method($method) !!}

        @include('partials.autocomplete_fix')
        @if ($vendor)
            {!! Former::populate($vendor) !!}
            {!! Former::populateField('task_rate', floatval($vendor->task_rate) ? Utils::roundSignificant($vendor->task_rate) : '') !!}
            {!! Former::populateField('show_tasks_in_portal', intval($vendor->show_tasks_in_portal)) !!}
            {!! Former::populateField('send_reminders', intval($vendor->send_reminders)) !!}
            {!! Former::hidden('public_id') !!}
        @else
            {!! Former::populateField('bill_number_counter', 1) !!}
            {!! Former::populateField('quote_number_counter', 1) !!}
            {!! Former::populateField('send_reminders', 1) !!}
            @if ($account->vendor_number_counter)
                {!! Former::populateField('id_number', $account->getBillNextNumber()) !!}
            @endif
        @endif
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading" style="color:white;background: #777 !important;">
                        <h3 class="panel-title in-bold-white">{!! trans('texts.company_details') !!}</h3>
                    </div>
                    <div class="panel-body">
                        <!-- company details -->
                    {!! Former::text('name')->label('texts.company_name')->data_bind("attr { placeholder: placeholderName }") !!}
                    {!! Former::text('id_number')->placeholder($account->vendorNumbersEnabled() ? $account->getBillNextNumber() : ' ') !!}
                    {!! Former::text('vat_number') !!}
                    {!! Former::text('website') !!}
                    {!! Former::text('work_phone') !!}
                    <!-- vendor type -->
                    {{--                    {!! Former::select('vendor_type_id')->addOption('', '')--}}
                    {{--                    ->placeholder(trans('texts.select_vendor_type'))--}}
                    {{--                    ->label(trans('texts.vendor_type_name'))--}}
                    {{--                    ->fromQuery($vendorTypes, 'name', 'id') !!}--}}

                    <!-- vendor hold reason -->
                        {!! Former::select('hold_reason_id')->addOption('', '')
                        ->placeholder(trans('texts.select_hold_reason'))
                        ->label(trans('texts.hold_reason'))
                        ->fromQuery($holdReasons, 'name', 'id') !!}

                        @include('partials/custom_fields', ['entityType' => ENTITY_VENDOR])
                        @if ($account->usesBillCounter())
                            {!! Former::text('bill_number_counter')->label('bill_counter') !!}
                            @if (! $account->share_counter)
                                {!! Former::text('quote_number_counter')->label('quote_counter') !!}
                            @endif
                        @endif
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" style="color:white;background: #777 !important;">
                        <h3 class="panel-title in-bold-white">{!! trans('texts.address') !!}</h3>
                    </div>
                    <div class="panel-body">
                        <div role="tabpanel">
                            <ul class="nav nav-tabs" role="tablist" style="border: none">
                                <li role="presentation" class="active">
                                    <a href="#billing_address" aria-controls="billing_address" role="tab"
                                       data-toggle="tab">{{ trans('texts.billing_address') }}</a>
                                </li>
                                <li role="presentation">
                                    <a href="#shipping_address" aria-controls="shipping_address" role="tab"
                                       data-toggle="tab">{{ trans('texts.shipping_address') }}</a>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content" style="padding-top:24px;">
                            <div role="tabpanel" class="tab-pane active" id="billing_address">
                                {!! Former::text('address1') !!}
                                {!! Former::text('address2') !!}
                                {!! Former::text('city') !!}
                                {!! Former::text('state') !!}
                                {!! Former::text('postal_code')
                                ->oninput(config('ninja.google_maps_api_key') ? 'lookupPostalCode()' : '') !!}
                                {!! Former::select('country_id')->addOption('','')
                                ->autocomplete('off')
                                ->fromQuery($countries, 'name', 'id') !!}

                                <div class="form-group" id="copyShippingDiv" style="display:none;">
                                    <label for="city" class="control-label col-lg-4 col-sm-4"></label>
                                    <div class="col-lg-8 col-sm-8">
                                        {!! Button::normal(trans('texts.copy_shipping'))->small() !!}
                                    </div>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="shipping_address">
                                {!! Former::text('shipping_address1')->label('address1') !!}
                                {!! Former::text('shipping_address2')->label('address2') !!}
                                {!! Former::text('shipping_city')->label('city') !!}
                                {!! Former::text('shipping_state')->label('state') !!}
                                {!! Former::text('shipping_postal_code')
                                ->oninput(config('ninja.google_maps_api_key') ? 'lookupPostalCode(true)' : '')
                                ->label('postal_code') !!}
                                {!! Former::select('shipping_country_id')->addOption('','')
                                ->autocomplete('off')
                                ->fromQuery($countries, 'name', 'id')->label('country_id') !!}
                                <div class="form-group" id="copyBillingDiv" style="display:none;">
                                    <label for="city" class="control-label col-lg-4 col-sm-4"></label>
                                    <div class="col-lg-8 col-sm-8">
                                        {!! Button::normal(trans('texts.copy_billing'))->small() !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading" style="color:white;background: #777 !important;">
                        <h3 class="panel-title in-bold-white">{!! trans('texts.contacts') !!}</h3>
                    </div>
                    <div class="panel-body">
                        <div data-bind='template: { foreach: contacts,
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
                            @if ($account->hasFeature(FEATURE_CLIENT_PORTAL_PASSWORD) && $account->enable_portal_password)
                                {!! Former::password('password')->data_bind("value: password()?'-%unchanged%-':'', valueUpdate: 'afterkeydown',
                                attr: {name: 'contacts[' + \$index() + '][password]'}")->autocomplete('new-password')->data_lpignore('true') !!}
                            @endif
                            @if (Auth::user()->hasFeature(FEATURE_INVOICE_SETTINGS))
                                @if ($account->customLabel('contact1'))
                                    @include('partials.custom_field', [
                                    'field' => 'custom_contact1',
                                    'label' => $account->customLabel('contact1'),
                                    'databind' => "value: custom_value1, valueUpdate: 'afterkeydown',
                                    attr: {name: 'contacts[' + \$index() + '][custom_value1]'}",
                                    ])
                                @endif
                                @if ($account->customLabel('contact2'))
                                    @include('partials.custom_field', [
                                    'field' => 'custom_contact2',
                                    'label' => $account->customLabel('contact2'),
                                    'databind' => "value: custom_value2, valueUpdate: 'afterkeydown',
                                    attr: {name: 'contacts[' + \$index() + '][custom_value2]'}",
                                    ])
                                @endif
                            @endif

                            <div class="form-group">
                                <div class="col-lg-8 col-lg-offset-4 bold">
                                <span class="redlink bold" data-bind="visible: $parent.contacts().length > 1">
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
                    <div class="panel-heading" style="color:white;background: #777 !important;">
                        <h3 class="panel-title in-bold-white">{!! trans('texts.additional_info') !!}</h3>
                    </div>
                    <div class="panel-body">
                        <div role="tabpanel">
                            <ul class="nav nav-tabs" role="tablist" style="border: none">
                                <li role="presentation" class="active">
                                    <a href="#settings" aria-controls="settings" role="tab"
                                       data-toggle="tab">{{ trans('texts.settings') }}</a>
                                </li>
                                <li role="presentation">
                                    <a href="#notes" aria-controls="notes" role="tab"
                                       data-toggle="tab">{{ trans('texts.notes') }}</a>
                                </li>
                                @if (Utils::isPaidPro())
                                    <li role="presentation">
                                        <a href="#messages" aria-controls="messages" role="tab"
                                           data-toggle="tab">{{ trans('texts.messages') }}</a>
                                    </li>
                                @endif
                                <li role="presentation">
                                    <a href="#classify" aria-controls="classify" role="tab"
                                       data-toggle="tab">{{ trans('texts.classify') }}</a>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content" style="padding-top:24px;">
                            <div role="tabpanel" class="tab-pane active" id="settings">
                                {!! Former::select('currency_id')->addOption('','')
                                ->placeholder(trans('texts.select_currency'))
                                ->fromQuery($currencies, 'name', 'id') !!}

                                {!! Former::select('language_id')->addOption('','')
                                ->placeholder(trans('texts.select_language'))
                                ->fromQuery($languages, 'name', 'id') !!}

                                {!! Former::select('payment_terms')->addOption('','')
                                ->fromQuery(\App\Models\PaymentTerm::getSelectOptions(), 'name', 'num_days')
                                ->placeholder(trans('texts.select_payment_term'))
                                ->help(trans('texts.payment_terms_help') . ' | ' . link_to('/settings/payment_terms', trans('texts.customize_options')))
                                !!}

                                @if ($account->hasReminders())
                                    {!! Former::checkbox('send_reminders')
                                    ->text('send_vendor_reminders')
                                    ->label('reminders')
                                    ->value(1) !!}
                                @endif
                            </div>
                            <div role="tabpanel" class="tab-pane" id="notes">
                                {!! Former::textarea('public_notes')->rows(6) !!}
                                {!! Former::textarea('private_notes')->rows(6) !!}
                            </div>
                            @if (Utils::isPaidPro())
                                <div role="tabpanel" class="tab-pane" id="messages">
                                    @foreach (App\Models\Common\Account::$customMessageTypes as $type)
                                        {!! Former::textarea('custom_messages[' . $type . ']')
                                        ->placeholder($account->customMessage($type))
                                        ->label($type) !!}
                                    @endforeach
                                </div>
                            @endif
                            <div role="tabpanel" class="tab-pane" id="classify">
                                <!-- industry employee size -->
                            {!! Former::select('size_id')->addOption('','')
                            ->fromQuery($sizes, 'name', 'id') !!}
                            <!-- industry category size -->
                                {!! Former::select('industry_id')->addOption('','')
                                ->fromQuery($industries, 'name', 'id') !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {!! Former::hidden('data')->data_bind("value: ko.toJSON(model)") !!}

        <script type="text/javascript">
            $(function () {
                $('#country_id, #shipping_country_id').combobox();

// show/hide copy buttons if address is set
                $('#billing_address').change(function () {
                    $('#copyBillingDiv').toggle(isAddressSet());
                });
                $('#shipping_address').change(function () {
                    $('#copyShippingDiv').toggle(isAddressSet(true));
                });

// button handles to copy the address
                $('#copyBillingDiv button').click(function () {
                    copyAddress();
                    $('#copyBillingDiv').hide();
                });
                $('#copyShippingDiv button').click(function () {
                    copyAddress(true);
                    $('#copyShippingDiv').hide();
                });

// show/hide buttons based on loaded values
                if ({{ $vendor && $vendor->hasAddress() ? 'true' : 'false' }}) {
                    $('#copyBillingDiv').show();
                }
                if ({{ $vendor && $vendor->hasAddress() ? 'true' : 'false' }}) {
                    $('#copyShippingDiv').show();
                }
            });

            function copyAddress(shipping) {
                var fields = [
                    'address1',
                    'address2',
                    'city',
                    'state',
                    'postal_code',
                    'country_id',
                ];
                for (var i = 0; i < fields.length; i++) {
                    var field1 = fields[i];
                    var field2 = 'shipping_' + field1;
                    if (shipping) {
                        $('#' + field1).val($('#' + field2).val());
                    } else {
                        $('#' + field2).val($('#' + field1).val());
                    }
                }
                $('#country_id').combobox('refresh');
                $('#shipping_country_id').combobox('refresh');
            }

            function isAddressSet(shipping) {
                var fields = [
                    'address1',
                    'address2',
                    'city',
                    'state',
                    'postal_code',
                    'country_id',
                ];
                for (var i = 0; i < fields.length; i++) {
                    var field = fields[i];
                    if (shipping) {
                        field = 'shipping_' + field;
                    }
                    if ($('#' + field).val()) {
                        return true;
                    }
                }
                return false;
            }

            function ContactModel(data) {
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
                            return new ContactModel(options.data);
                        }
                    }
                };
                if (data) {
                    ko.mapping.fromJS(data, self.mapping, this);
                } else {
                    self.contacts.push(new ContactModel());
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
                model.contacts.push(new ContactModel());
                return false;
            }

            model.removeContact = function () {
                model.contacts.remove(this);
            }

        </script>

        <!-- vendor action -->
        @if(Auth::user()->canCreateOrEdit(ENTITY_VENDOR))
            <center class="buttons">
                {!! Button::normal(trans('texts.cancel'))->large()->asLinkTo(URL::to('/vendors/' . ($vendor ? $vendor->public_id : '')))->appendIcon(Icon::create('remove-circle')) !!}
                {!! Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')) !!}
            </center>
        @endif
        {!! Former::close() !!}
    </div>
@stop
