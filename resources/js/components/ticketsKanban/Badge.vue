<template>
    <div
        class="pull-right"
        :class="`text-${color}`"
        :style="`background-color:${color};`"
    >
        <span
            class="w-2 h-2 rounded-full mr-1"
            :style="`background-color:${color};`"
        ></span>
        <span>
            <slot></slot>
        </span>
    </div>
</template>
<script>
export default {
    props: {
        color: {
            type: String,
            default: "teal"
        }
    }
};
</script>
