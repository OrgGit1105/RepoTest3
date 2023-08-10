<template>
  <div id="wrapper" :class="toggle">
    <!-- <Sidebar /> -->
    <div id="page-content-wrapper">
      <Navbar @toggle="toggleMenu()" />
      <div>
        <AppMain />
      </div>
      <a class="button-chart" @click="showModalChat = true"><img class="custom-image" :src="logo" alt="V-FACE"></a>
      <div v-if="showModalChat" class="modal-chart">
        <div class="modal-header chart-header">
          <strong>V-Face x GPT</strong>
          <button class="close close-chart" @click="showModalChat = false"><span>&ndash;</span></button>
        </div>
        <div class="modal-content chart-content">
          <div v-for="arrayChart in arrayCharts" :key="arrayChart.id">
            <div class="content-right">
              <p> {{ arrayChart.que }}</p>
              <div><img class="custom-image" :src="logoImage" alt="V-FACE"></div>
            </div>
            <div class="content-left">
              <div><img class="custom-image" :src="logoImage" alt="V-FACE"></div>
              <p>{{ arrayChart.result }}</p>
            </div>
          </div>
        </div>
        <div class="modal-footer chart-footer">
          <form @submit.prevent="hendaleSubmitChatGPT">
            <input v-model="questions" type="text" class="chart-input">
            <button class="chart-submit" type="submit">Send</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Navbar from './components/Navbar/index';
// import Sidebar from './components/Sidebar/index';
import AppMain from './components/AppMain';
import { resultChatGPT } from '../api/schedules';
const logo = require('@/assets/images/chatgpt-icon.png');
const logoImage = require('@/assets/images/logo.png');

export default {
  name: 'Layout',
  components: {
    AppMain,
    // Sidebar,
    Navbar,
  },
  data() {
    return {
      toggle: '',
      toggleBool: false,
      logo,
      logoImage,
      showModalChat: false,
      questions: '',
      result_question: '',
      arrayCharts: [],
    };
  },
  methods: {
    toggleMenu() {
      this.toggleBool = !this.toggleBool;
      if (this.toggleBool === true) {
        this.toggle = 'toggled';
      } else {
        this.toggle = '';
      }
    },
    async hendaleSubmitChatGPT() {
      const PARAMS = {
        question: this.questions,
      };
      await resultChatGPT(PARAMS)
        .then((response) => {
          if (response.code === 200) {
            console.log(response.data);
            const newChart = { que: this.questions, result: response.data };
            if (this.questions !== null) {
              this.arrayCharts.unshift(newChart);
            }
            this.questions = '';
          }
        }).catch((error) => {
          console.log(error);
        });
    },
  },
};
</script>

<style scoped>
#nav {
  padding: 30px;
}

#nav a {
  font-weight: bold;
  color: #2c3e50;
}
body {
  overflow-x: hidden;
}
#nav a.router-link-exact-active {
  color: #42b983;
}
.modal-chart {
  position: fixed;
  bottom: 10%;
  right: 2%;
  width: 25%;
  height: 45%;
  background-color: white;
  border: 1px solid rgba(0, 0, 0, 0.2);
  border-top-left-radius: calc(0.3rem - 1px) !important;
  border-top-right-radius: calc(0.3rem - 1px);
  z-index: 9999;
}

.button-chart {
  position: fixed;
  bottom: 10%;
  right: 2%;
  z-index: 9998;
  cursor: pointer;
}

.chart-content {
  height: 72% !important;
  border: none !important;
}

.chart-input {
  width: 80%;
  border-radius: 6px;
  border: none;
  padding: 3px;
}

.chart-input:focus {
  border: none;
}

.chart-header {
  background-color: #0070c9;
  color: white;
  position: relative;
}

.chart-header strong {
  position: inherit;
  left: 40%;
}

.close-chart {
  background-color: white;
  opacity: initial !important;
  padding: revert !important;
  margin: inherit !important;
  border-radius: 6px;
}

.custom-image {
  width: 55px;
}

.chart-footer {
  background-color: #E6E6E6;
  flex-wrap: nowrap;
}
.chart-footer form {
  width: 100%;
}

.chart-submit {
  background-color: #40729A;
  color: white;
  border-radius: 10%;
  border: none;
  padding: 3px;
  width: 16%;
}

.content-right {
  text-align: right;
  display: flex;
  align-self: flex-end;
}

.content-right p {
  margin: auto;
  margin-right: 0px;
}

.content-left {
  align-self: initial;
  text-align: left;
  display: flex;
  max-width: max-content;
}

.content-left p {
  margin: auto;
}

.modal-content {
  flex-direction: column-reverse !important;
  overflow: auto;
}
</style>
