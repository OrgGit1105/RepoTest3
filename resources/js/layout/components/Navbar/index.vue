/* eslint-disable vue/max-attributes-per-line */
<template>
  <b-navbar style="background: #dfe3e7" toggleable="lg" class="py-3 px-5">
    <b-navbar-brand href="#" class="logo">
      <Logo :href="'#'" />
    </b-navbar-brand>

    <b-navbar-toggle target="nav-collapse" />
    <b-collapse id="nav-collapse" style="display: none; font-size: 22px; white-space: nowrap" is-nav>
      <div style="display: flex; flex: 1; justify-content: space-between">
        <div style="margin-left: 3rem; display: flex; flex-direction: row; justify-content: space-evenly">
          <b-navbar-nav v-for="(item, index) in navbars" :key="index" ref="ListRoutes">
            <b-nav-item @click="$router.push({ path: item.href })">{{ item.name }}</b-nav-item>
          </b-navbar-nav>
          <b-navbar-nav>
            <b-nav-item-dropdown v-if="!checkViam" right>
              <template #button-content>
                <span>VIAM</span>
              </template>
              <b-dropdown-item @click="$router.push({ path: `/viam/index` })">VIAM POLICY</b-dropdown-item>
              <b-dropdown-item @click="$router.push({ path: `/viam-user/index` })">VIAM USER</b-dropdown-item>
            </b-nav-item-dropdown>
          </b-navbar-nav>
        </div>
        <div>
          <b-navbar-nav>
            <b-nav-item-dropdown right>
              <template #button-content>
                <span :style="{ color: auth ? '#0070C9' : '' }">{{ auth ? auth.name : '' }}</span>
              </template>
              <b-dropdown-item v-if="auth && auth.role_id === 1" @click="$router.push({ path: `/user/edit/${auth.id}` })">Profile</b-dropdown-item>
              <b-dropdown-item @click="doLogout()">Sign Out</b-dropdown-item>
            </b-nav-item-dropdown>
          </b-navbar-nav>
        </div>
      </div>
    </b-collapse>
  </b-navbar>
</template>
<script>
const noAvt = require('@/assets/images/noavt.png');
import Logo from '@/layout/components/Logo';
export default {
  name: 'Navbar',
  components: {
    Logo,
  },
  data() {
    return {
      noAvt,
      listOne: false,
      listTwo: false,
      listThree: false,
      navbars: [],
      navbarAdmin: [
        { name: 'Schedules', href: '/schedules/index' },
        { name: 'Working time', href: '/working-time/index' },
        { name: 'Analytics', href: '/analytics/index' },
        { name: 'Employee', href: '/user/index' },
      ],
      navbarUser: [
        { name: 'Schedules', href: '/schedules/index' },
        { name: 'Analytics', href: '/analytics/index' },
      ],
    };
  },
  computed: {
    auth() {
      return this.$store.state.user.userInfo;
    },
    email() {
      // console.log('Thong tin cua b', this.$store.getters.role_id);
      return this.$store.getters.email;
    },
    title() {
      return this.$route.meta.title;
    },
    avt() {
      if (this.$store.getters.avatar) {
        return process.env.MIX_STORE_IMAGE_URL + this.$store.getters.avatar;
      } else {
        return null;
      }
    },
    currentPage() {
      return this.$route.path;
    },
    checkViam() {
      let check = false;
      const roles = this.$store.getters.role_id;
      if (roles === 1){
        check = true;
      }
      return !check;
    },
  },
  created() {
    // this.getMonthAndYear();
    this.navbarSetting();
  },
  methods: {
    doLogout() {
      this.$store.dispatch('user/logout').then(() => {
        this.$router.push('/login');
      });
    },
    navbarSetting() {
      if (this.$store.getters.role_id === 1) {
        this.navbars = this.navbarAdmin;
      } else {
        this.navbars = this.navbarUser;
      }
    },
  },
};
</script>

<style lang="scss" scoped>
::after,
::before {
  box-sizing: border-box;
}
.logo img {
  width: 180px;
}
.navbar-nav .nav-item .nav-link:hover {
  background: transparent;
  border-radius: inherit;
  color: #000000;
}
nav.navbar.navbar-dark.navbar-expand-lg {
  border-bottom: 1px solid #ccc;
  box-shadow: 0px 0px 16px 0px rgb(0 0 0 / 10%);
}
.menu {
  display: flex;
}
.ul1 > li > a {
  display: block;
  padding: 10px;
  font-weight: 500;
  text-transform: uppercase;
}
.menu a {
  display: block;
  text-decoration: none;
}
.title {
  color: black;
}
.menu li {
  display: block;
  // float: left;
  position: relative;
  // min-width: 290px;
}
.menu .li1 {
  margin: 0px 10px;
  // padding: 0px 10px;
  min-width: 220px;
}
.menu .li1 > a:hover {
  color: #0f68b1;
}

.menu li ul {
  position: absolute;
  left: 0;
  min-width: 220px;
  top: 99px;
  margin: 0;
  padding: 0;
  background: #fff;
  text-align: center;
  box-shadow: 0 1px 2px 2px rgb(0 0 0 / 20%);
  z-index: 999999;
  width: 100%;
}

.menu li ul li {
  transition: background 0.2s;
}

.menu li ul li:hover {
  background: #fb9a09;
  cursor: pointer;
  color: #fff;
}
.menu li ul li a:hover {
  color: #fff !important;
}

.menu-li1 {
  color: #000;
  text-transform: uppercase;
  font-weight: 500;
  padding: 0px 5px;
  height: 99px;
  line-height: 99px;
  font-size: 19px;
  text-align: center;
  letter-spacing: 2px;
}

.menu .menu-li1::before {
  content: "";
  width: 100%;
  height: 5px;
  position: absolute;
  left: 0;
  bottom: 0;
  background: #fff;
  transition: 0.5s transform ease;
  transform: scale3d(0, 1, 1);
  transform-origin: 0 50%;
}
.menu .menu-li1:hover::before {
  transform: scale3d(1, 1, 1);
}
.menu .menu-li1::before {
  background: #fb9a09;
  transform-origin: 50% 50%;
}
.full-name {
  font-weight: 500;
}
.btn-logout {
  position: relative;
  border: 1px solid #0f68b1;
  color: #0f68b1;
}

.btn-logout:hover {
  background: #0f68b1 !important;
  color: #fff;
  border: 1px solid #0f68b1;
}

.dropdown-content {
  display: none;
  position: absolute;
  background-color: #f1f1f1;
  min-width: 160px;
  box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
  z-index: 1;
}

.dropdown-content a {
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
}

.dropdown:hover .dropdown-content {
  display: block;
}

.dropdown:hover .dropbtn {
  background-color: whitesmoke;
}
.text {
  text-align: center;
}

.dropdown button {
  background-color: white;
  color: black;
  border: none;
  font-size: 18px;
  text-transform: uppercase;
}

button.navbar-toggler {
  background: #052c50 99%;
}

button.navbar-toggler > svg {
  color: white;
}

button.navbar-toggler:focus {
  outline: none;
}
::v-deep .custom-icon .dropdown-toggle::after {
  display: inline-block;
  margin-left: 0.255em;
  vertical-align: 0.255em;
  content: "";
  border-top: 0.3em solid #0070C9;
  border-right: 0.3em solid transparent;
  border-bottom: 0;
  border-left: 0.3em solid transparent;
}
::v-deep .custom-icon-viam .dropdown-toggle::after {
  display: inline-block;
  margin-left: 0.255em;
  vertical-align: 0.255em;
  content: "";
  border-top: 0.3em solid #54575a;
  border-right: 0.3em solid transparent;
  border-bottom: 0;
  border-left: 0.3em solid transparent;
}
.navbar-brand {padding: 0 !important;}
</style>
