<script>
import Vue from 'vue'
import App from './App'

import fullCalendar from 'vue-fullcalendar'

Vue.component('full-calendar', fullCalendar)

// Vue2
new Vue({
  el : '#app',
  render: h => h(App),
  template : '<App/>',
  components : {
    App
  }
})

//Vue1

new Vue({
	el : 'body',
	components : {
		App
	}
})
</script>