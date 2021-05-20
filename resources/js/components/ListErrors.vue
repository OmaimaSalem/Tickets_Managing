<template>
  <div class="alert alert-danger" v-show="Object.keys(errors).length > 0">
    <ul class="error-messages m-0">
      <li v-for="(value, key) in errors" :key="key">
        <span v-for="err in value" :key="err" v-text="err" />
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  props: {
    errors: { type: Object, required: true }
  }
};
</script>
