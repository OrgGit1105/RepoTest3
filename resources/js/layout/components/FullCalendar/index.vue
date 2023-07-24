<script>
import Vue from 'vue';
import App from './App';

import fullCalendar from 'vue-fullcalendar';

Vue.component('FullCalendar', fullCalendar);

// Vue2
new Vue({
  el: '#app',
  components: {
    App,
  },
  render: h => h(App),
  template: '<App/>',
});

// Vue1

new Vue({
  el: 'body',
  components: {
    App,
  },
});
</script>
