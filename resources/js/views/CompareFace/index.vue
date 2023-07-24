<template>
  <div class="container">
    <!--    <div v-if="type === ''">-->
    <!--      <b-button>IN</b-button>-->
    <!--      <b-button>OUT</b-button>-->
    <!--    </div>-->
    <div v-if="type === ''" class="row">
      <div class="col-md-6">
        <h2>Current Camera</h2>
        <code v-if="device">{{ device.label }}</code>
        <div class="border">
          <vue-web-cam
            ref="webcam"
            :device-id="deviceId"
            width="100%"
            @started="onStarted"
            @stopped="onStopped"
            @error="onError"
            @cameras="onCameras"
            @camera-change="onCameraChange"
          />
        </div>

        <div class="row">
          <div class="col-md-12">
            <select v-model="camera">
              <option>-- Select Device --</option>
              <option
                v-for="deviceDetail in devices"
                :key="deviceDetail.deviceId"
                :value="deviceDetail.deviceId"
              >{{ deviceDetail.label }}</option>
            </select>
          </div>
          <div class="col-md-12">
            <button type="button" class="btn btn-primary" @click="onCapture">Capture Photo</button>
            <button type="button" class="btn btn-danger" @click="onStop">Stop Camera</button>
            <button type="button" class="btn btn-success" @click="onStart">Start Camera</button>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <h2>Captured Image</h2>
        <figure class="figure">
          <img :src="img" class="img-responsive">
        </figure>
      </div>
    </div>
  </div>
</template>

<script>
// Import API

import { WebCam } from 'vue-web-cam';
import * as ImageApi from '../../api/image_face';
import { MakeToast } from '../../utils/toast_message';

export default {
  name: 'CompareFace',
  components: {
    'vue-web-cam': WebCam,
  },
  data() {
    return {
      img: null,
      camera: null,
      deviceId: null,
      devices: [],
      type: '',
    };
  },
  computed: {
    device: function() {
      return this.devices.find(n => n.deviceId === this.deviceId);
    },
  },
  watch: {
    camera: function(id) {
      this.deviceId = id;
    },
    devices: function() {
      // Once we have a list select the first one
      const [first] = this.devices;
      if (first) {
        this.camera = first.deviceId;
        this.deviceId = first.deviceId;
      }
    },
  },
  methods: {
    onCapture() {
      this.img = this.$refs.webcam.capture();
      const image = new FormData();
      image.append('file', this.img);
      image.append('type', 'WithoutMask');
      image.append('registration_type', 'pc');

      ImageApi.compareFaceImage(image)
        .then((response) => {
          if (response.code === 200){
            MakeToast({
              variant: 'success',
              title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
              content: `Success. The face is matched to Employee name ${response.data.profile.name}`,
            });

            // const TOKEN = response.data.access_token;
            // const PROFILE = response.data.profile;
            //
            // const USER = {
            //   id: PROFILE.id || '',
            //   name: PROFILE.name || '',
            //   email: PROFILE.email || '',
            //   role_id: PROFILE.role_id || '',
            //   status: PROFILE.status || '',
            // };
            // this.$store
            //   .dispatch('user/saveLogin', { USER, TOKEN })
            //   .then(() => {
            //     MakeToast({
            //       variant: 'success',
            //       title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
            //       content: this.$t('LANGUAGES.TEXT_TOAST_CONTENT_LOGIN_SUCCESSFULLY'),
            //     });
            //     if (this.$route.params.redirect){
            //       this.$router.push('this.$route.params.redirect');
            //     } else {
            //       this.$router.push('/working-time/index');
            //     }
            //     this.closeLoading();
            //   })
            //   .catch(() => {
            //     console.error('Can not saveLogin!');
            //   });
          } else {
            MakeToast({
              variant: 'warning',
              title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
              content: response.message,
            });
          }
        })
        .catch((error) => {
          MakeToast({
            variant: 'warning',
            title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
            content: error.message,
          });
        });
    },
    onStarted(stream) {
      console.log('On Started Event', stream);
    },
    onStopped(stream) {
      console.log('On Stopped Event', stream);
    },
    onStop() {
      this.$refs.webcam.stop();
    },
    onStart() {
      this.$refs.webcam.start();
    },
    onError(error) {
      console.log('On Error Event', error);
    },
    onCameras(cameras) {
      this.devices = cameras;
      console.log('On Cameras Event', cameras);
    },
    onCameraChange(deviceId) {
      this.deviceId = deviceId;
      this.camera = deviceId;
      console.log('On Camera Change Event', deviceId);
    },
  },
};
</script>
