<template>
  <div id="app">
    <router-view />
    <Loading />
  </div>
</template>

<script>
import Loading from '../layout/components/Loading/index.vue';

export default {
  name: 'App',
  components: {
    Loading,
  },
  data() {
    return {};
  },
  created() {
    console.log('Develop version 1.4.3');
  },
};

</script>
