<tr {!! $eval !!} id="resource-input-check-{{ $field }}">
    <td class="w-25 field-title">
        <div class="d-flex flex-column">
            @if(@$label)
            <b class="input-title">{!! $label !!}</b>
            @endif
            @if(@$description)
            <small class="mt-1 text-muted">
                {!! $description !!}
            </small>
            @endif
        </div>
    </td>
    <td>
        <div class='d-flex flex-column'>
            {!! @$slot_top !!}
            <el-switch :disabled='{{$disabled}}' class='ml-3' v-model='{{"form.$field"}}' active-color='{{$active_color}}' inactive-color='{{$inactive_color}}' active-text='{{$active_text}}' inactive-text='{{$inactive_text}}'>
            </el-switch>
            {!! @$slot_bottom !!}
        </div>
    </td>
</tr>