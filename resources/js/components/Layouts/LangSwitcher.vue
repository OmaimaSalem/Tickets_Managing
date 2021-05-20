<template>
  <div class="locale-changer">
    <select v-model="$i18n.locale">
      <option v-for="(lang, i) in langs" :key="lang.value" :value="lang.value">{{ lang.name }}</option>
    </select>
  </div>
</template>
<script>
export default {
  name: 'locale-changer',
  data () {
    return {
      langs: [
        {
          name: "German",
          value: 'de'
        },
        {
          name: "English",
          value: 'en'
        },
      ]
    }
  }
}
</script>
