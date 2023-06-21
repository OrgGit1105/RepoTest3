<template>
  <div class="logo">
    <a :href="href">
      <img class="custom-image" :src="logo" alt="V-FACE">
      <span>V-Face</span>
    </a>
  </div>
</template>

<script>
const logo = require('@/assets/images/logo.png');
export default {
  name: 'Logo',
  props: {
    href: {
      required: true,
      type: String,
      default: '#',
    },
  },
  data() {
    return {
      logo,
    };
  },
};
</script>

<style>
.logo {
  display: flex;
  justify-content: center;
}
.logo a {
  display: flex;
  align-items: center;
  color: #000;
}
.logo span {
  font-size: 35px;
  font-weight: 600;
  margin-left: 20px;
  text-transform: uppercase;
}
a:hover {
  color: #000!important;
  text-decoration: none!important;
}
.custom-image {
  width: 55px;
}
</style>
