<template>
  <div class="logo">
    <a :href="href">
      <img :src="logo" alt="V-FACE">
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
  font-size: 60px;
  font-weight: 600;
  margin-left: 5px;
}
</style>
