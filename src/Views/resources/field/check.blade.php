<tr {!! $eval !!} id="resource-input-check-{{ $field }}">
    <td class="field-title">
        <div class="flex flex-col">
            @if(@$label)
            <b class="input-title text-neutral-700 text-sm">{!! $label !!}</b>
            @endif
            @if(@$description)
            <small class="text-neutral-400 mt-1 input-title text-xs break-words">
                {!! $description !!}
            </small>
            @endif
        </div>
    </td>
    <td>
        <div class='flex flex-col'>
            {!! @$slot_top !!}
            <el-switch :disabled='{{$disabled}}' class='ml-3' v-model='{{"form.$field"}}' active-color='{{$active_color}}' inactive-color='{{$inactive_color}}' active-text='{{$active_text}}' inactive-text='{{$inactive_text}}'>
            </el-switch>
            {!! @$slot_bottom !!}
        </div>
    </td>
</tr>