<template>
    <iframe :src="src" frameborder="0" @load="onLoad" :style="{width : '100%'}" />
</template>
  
<script>
import iframeResize from 'iframe-resizer/js/iframeResizer';

export default {
    props: ['src'],
    methods: {
        onLoad(value, el) {
            iframeResize(value, el);
        }
    }
};
</script>