<template>
  <b-dropdown v-if="showLang" id="dropdown-lang" text="Block Level Dropdown" block>
    <template #button-content class="btn-lang">
      <b-icon icon="globe" font-scale="1.0" class="icon-globe" />

      <span>{{ $t('views.login.changelang') }}</span>
    </template>

    <b-dropdown-item
      :disabled="language === 'en'"
      @click="handleSetLanguage('en')"
    >
      {{ $t('navbar.languages.en') }}
    </b-dropdown-item>

    <b-dropdown-item
      :disabled="language === 'ja'"
      @click="handleSetLanguage('ja')"
    >
      {{ $t('navbar.languages.ja') }}
    </b-dropdown-item>
  </b-dropdown>
</template>

<script>
import { MakeToast } from '@/utils/toast_message';

export default {
  data() {
    return {
      showLang: process.env.MIX_SHOW_LANG === 'true',
    };
  },
  computed: {
    language() {
      return this.$store.getters.language;
    },
  },
  methods: {
    handleSetLanguage(lang) {
      this.$i18n.locale = lang;
      this.$store.dispatch('app/setLanguage', lang);
      var title = this.$t('notiChangeLang.title');
      var content = this.$t('notiChangeLang.content');
      MakeToast({ variant: 'success', title: title, content: content });
    },
  },
};
</script>

<style scoped>
.icon-globe {
  margin-right: 8px;
}
::v-deep #dropdown-lang__BV_toggle_ {
    width: 100%;
    background-color: white;
    color: dodgerblue;
    border: 0,5 px solid;
    border-color: cornflowerblue;
}
</style>
