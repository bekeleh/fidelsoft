<!-- Multiple item selection  -->
<div class="form-group {{ $errors->has($field_name) ? 'has-error' : '' }}">
    <label class="col-md-3 control-label" for="{{$label}}">{{trans('texts.item_list')}}</label>
    <div class="col-xs-12">
        <div class="controls">
            <select name="{{$field_name}}[]"
                    id="{{$field_name}}" autofocus="autofocus" minlength="1" multiple="multiple"
                    size="12" required="required" class="form-control">
                @if ($items = Input::old($field_name, (isset($object->$field_name)) ? $object->$field_name : ''))
                    @if(is_array($items))
                        @foreach($items as $item_id)
                            <option value="{{$item_id}}" selected="selected">
                                {{ (\App\Models\Product::find($item_id)) ? (\App\Models\Product::find($item_id)->name) : '' }}
                            </option>
                        @endforeach
                    @else
                        <option value="{{$items}}" selected="selected">
                            {{ (\App\Models\Product::find($items)) ? (\App\Models\Product::find($items)->name) : '' }}
                        </option>
                    @endif
                @else
                    <option value=""></option>
                @endif
            </select>
            <span class="help-block alert alert-info">
            {{ Form::checkbox($check_item_name, '1', Input::old($check_item_name), array('id'=>$check_item_name,'enabled' => 'enabled')) }}
                {{ trans('texts.allQty') }}
        </span>
            {!! $errors->first($field_name, '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}
        </div>
    </div>
</div>
