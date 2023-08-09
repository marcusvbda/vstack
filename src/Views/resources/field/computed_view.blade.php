<v-computed-view label="{{ $label }}" description="{{ $description }}" eval_script="{{ base64_encode($eval) }}"
    template_script='{{ base64_encode($template) }}' :form="form">
    <template #prepend-slot>
        {!! @$slot_top !!}
    </template>
    <template #append-slot>
        {!! @$slot_bottom !!}
    </template>
</v-computed-view>
