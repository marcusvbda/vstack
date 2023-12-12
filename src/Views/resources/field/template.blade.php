<tr class="flex flex-col md:table-row">
    <td class="field-title">
        <div class="flex flex-col">
            @if (@$label)
                <b class="input-title text-neutral-700 text-sm">
                    {{ $label }}
                    @if (@$required)
                        <small style="position: relative;top: -2px;color:#961313;font-weight: bold;">*</small>
                    @endif
                </b>
            @endif
            @if (@$label)
                <small class="text-neutral-400 mt-1 input-title text-xs break-words">
                    {{ $description }}
                </small>
            @endif
        </div>
    </td>
    <td class="field-slot">
        @yield('slot')
    </td>
</tr>
