<template>
  <div class="login-page">
    <div class="bg-page" :style="`background-image: url(${bgLogin})`" />
    <section>
      <div id="loginArea">
        <Logo />
        <div id="login-form">
          <div class="form-login-dx">
            <div class="zone-login">
              <b-form>
                <div>
                  <!-- User Name -->
                  <b-form-group>
                    <label class="label-name d-flex">ID</label>
                    <b-form-input
                      id="txtUserName"
                      v-model="account.username"
                      dusk="username"
                      class="mb-0 px-2 inputForm"
                      :placeholder="$t('LANGUAGES.TEXT_PLACEHOLDER_EMAIL')"
                      type="text"
                      spellcheck="false"
                      :state="error.username"
                      :formatter="formatText"
                      @input="handleChangeForm($event, 'username')"
                    />
                    <b-form-invalid-feedback :state="error.username" class="pl-1">
                      {{
                        $t('LANGUAGES.ERROR_YOUR_USER_NAME_NOT_NULL_AND_MUST_BE_A_EMAIL')
                      }}
                    </b-form-invalid-feedback>
                  </b-form-group>
                  <!-- Pass word -->
                  <b-form-group class="mb-0">
                    <label class="label-name d-flex">Password</label>
                    <div class="password-input">
                      <b-form-input
                        id="txtPassWord"
                        v-model="account.password"
                        dusk="password"
                        class="mb-0 px-2 inputForm"
                        :type="typePassword"
                        :state="error.password"
                        :placeholder="$t('LANGUAGES.TEXT_PLACEHOLDER_PASSWORD')"
                        :formatter="formatText"
                        @keyup.enter="handleLogin()"
                        @input="handleChangeForm($event, 'password')"
                      />
                    </div>
                    <b-form-invalid-feedback :state="error.password" class="pl-1">
                      {{
                        $t('LANGUAGES.ERROR_YOUR_PASSWORD_MUST_BE_GREATER_THAN_8_CHARACTERS_AND_LESS_THAN_16')
                      }}
                    </b-form-invalid-feedback>
                  </b-form-group>

                </div>
                <b-button class="btn btn_login" @click="handleLogin($event)">
                  <b-icon icon="play-circle" style="color: #323232; width: 42px; height: 42px" />
                </b-button>
              </b-form>
            </div>

          </div>
        </div>
      </div>

      <!-- <div id="registerArea">
        <Register />
      </div> -->
    </section>
  </div>
</template>

<script>
// Import API
const bgLogin = require('@/assets/images/bg-login.jpg');
import { postLogin } from '../../api/login';
import { MakeToast } from '../../utils/toast_message';
import Logo from '@/layout/components/Logo';

export default {
  name: 'Login',
  components: {
    Logo,
  },
  data() {
    return {
      // account Login
      account: {
        username: '',
        password: '',
      },
      error: {
        username: null,
        password: null,
      },
      typePassword: 'password',
      showPasswordStatus: 'eye-slash-fill',
      passwordLength: false,
      passwordText: '',
      showLoading: false,
      bgLogin: bgLogin,
    };
  },

  computed: {
    // showPasswordEye() {
    //   return this.account.password.length;
    // },
    language() {
      return this.$store.getters.language;
    },
  },

  watch: {
    showPasswordEye() {
      if (this.account && this.account.password && this.account.password.length){
        this.passwordLength = true;
        this.passwordText = this.$t('LANGUAGES.TEXT_SEE_PASSWORD');
      } else {
        this.passwordLength = false;
        this.passwordText = '';
      }
    },
  },
  // created() {
  //   this.$i18n.locale = 'ja';
  //   this.$store.dispatch('app/setLanguage', 'ja');
  // },
  methods: {
    async handleLogin(event) {
      this.checkValidate();
      if (this.checkValidate() === true) {
        const ACCOUNT = {
          url: '/auth/login',
          user_name: this.account.username,
          password: this.account.password,
        };
        this.openLoading();
        await postLogin(ACCOUNT)
          .then((response) => {
            if (response.code === 200) {
              // this.closeLoading();
              const TOKEN = response.data.access_token;
              const PROFILE = response.data.profile;

              const USER = {
                address: PROFILE.address || '',
                avatar: PROFILE.avatar || '',
                email: PROFILE.email || '',
                fax: PROFILE.fax || '',
                gender: PROFILE.gender || '',
                id: PROFILE.id || '',
                name: PROFILE.name || '',
                phone: PROFILE.phone || '',
                status: PROFILE.status || '',
                role_id: PROFILE.role_id || '',
                username: PROFILE.username || '',
              };

              this.$store
                .dispatch('user/saveLogin', { USER, TOKEN })
                .then(() => {
                  MakeToast({
                    variant: 'success',
                    title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
                    content: this.$t('LANGUAGES.TEXT_TOAST_CONTENT_LOGIN_SUCCESSFULLY'),
                  });

                  this.$router.push('/');
                  // this.closeLoading();
                })
                .catch(() => {
                  console.error('Can not saveLogin!');
                });
            } else if (response.code === 401) {
              this.closeLoading();
              MakeToast({
                variant: 'warning',
                title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
                content: response.message,
              });
            } else if (response.code === 422) {
              this.closeLoading();
              MakeToast({
                variant: 'warning',
                title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
                content: response.message,
              });
            }
          })
          .catch((error) => {
            // this.closeLoading();
            MakeToast({
              variant: 'danger',
              title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_FAILED'),
              content: error.message,
            });
          });
      } else {
        event.preventDefault();
        event.stopPropagation();
      }
    },
    checkValidate() {
      if (!this.account.username || !this.checkEmail(this.account.username)) {
        this.error.username = false;
        return false;
      } else if (!this.account.password || this.account.password.length < 8) {
        this.error.password = false;
        return false;
      } else {
        return true;
      }
    },
    handleRegister() {
      this.$router.push('/register');
    },
    openLoading() {
      this.$store.dispatch('loading/setLoading', true);
    },
    closeLoading() {
      this.$store.dispatch('loading/setLoading', false);
    },
    handForgotPassword() {
      this.$router.push('/remind-password');
    },
    checkEmail(email) {
      const checkEmail =
        /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      return checkEmail.test(email);
    },
    handleChangeForm(e, field) {
      const newValue = e;

      switch (field) {
        case 'username':
          if (newValue.length === 0 || !this.checkEmail(newValue)) {
            this.error.username = false;
          } else {
            this.error.username = true;
          }
          break;
        case 'password':
          if (newValue.length > 1 && newValue.length < 17) {
            this.error.password = true;
          } else {
            this.error.password = false;
          }
          break;
        default:
          break;
      }
    },
    showPassword() {
      if (this.typePassword === 'password') {
        this.typePassword = 'text';
        this.showPasswordStatus = 'eye-fill';
        this.passwordText = this.$t('LANGUAGES.TEXT_HIDE_PASSWORD');
      } else {
        this.typePassword = 'password';
        this.showPasswordStatus = 'eye-slash-fill';
        this.passwordText = this.$t('LANGUAGES.TEXT_SEE_PASSWORD');
      }
    },
    formatText(e) {
      return String(e).substring(0, 255);
    },
  },
};
</script>

<style lang="scss" scoped>
.login-page {
  position: relative;
  .bg-page {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0.1;
    background-position: bottom left;
    background-size: cover;
    background-repeat: no-repeat;
  }
}
#loginArea .logo {
  margin-bottom: 20px;
}
.login-form {padding: 40px;}
.title-login-form {
  border-top-left-radius: 10px;
  border-top-right-radius: 10px;
  text-align: center;
  padding: 20px;
  background: #0f68b1;
  color: #fff;
  display: flex;
}
    .bi-person-fill {
      font-size: 125%;
      margin-right:5px;
    }
        h6 {
    /* margin-bottom: 30px; */
    text-transform: uppercase;
    font-size: 14px;
}

section {
  position: relative;
  width: 100%;
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  overflow: hidden;
}

section h2 {
  position: relative;
  width: 100%;
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  overflow: hidden;
}

section a {
  position: relative;
  display: flex;
  justify-content: center;
  align-items: center;
  overflow: hidden;
}

section img {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  pointer-events: none;
}

section img#moon {
  top: -100%;
  mix-blend-mode: screen;
}

section img#stars {
  top: -100%;
  height: 100%;
  object-fit: cover;
}

section img#mountains_behind {
  bottom: -100%;
  top: initial;
}

section img#mountains_front {
  bottom: -110%;
  top: initial;
  z-index: 1000;
}

#text {
  position: absolute;
  color: #fff;
  font-size: 2vw;
  font-weight: 200;
}

#text span {
  font-weight: 700;
}

#btn {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  margin-top: 100px;
  text-decoration: none;
  display: inline-block;
  padding: 8px 50px;
  background: #fff;
  border-radius: 40px;
  font-size: 1.4em;
  color: #2b1055;
  z-index: 10000;
}

#registerArea {
  position: absolute;
  top: -2000px;
  justify-content: center;
  align-items: center;
}
h6 {
  padding-top: 4px;
}

.zone-login {
  position: relative;
  min-width: 500px;
  max-width: 500px;
  margin: 0 auto;
  background-color: #FEFEF1;
  border: 1px solid #F6DD74;
  border-radius: 20px;
  padding: 20px 0px;
  .password-input {
    position: relative;
    padding-right: 45px;
    #showPassword {
      cursor: pointer;
    }
  }

  .form-group {
    padding: 0px 20px;
    &:first-child {
      border-bottom: 1px solid #F6DD74;
      padding-bottom: 15px;
    }
    .label-name {
      font-size: 14px;
      font-weight: 700;
      color: #666;
      margin-bottom: 5px;
    }
    input {
      border: 0;
    }
  }
  .login-row {
    cursor: pointer;
  }

  .btn_login {
    position: absolute;
    bottom: 20px;
    right: 25px;
    z-index: 99;
    background-color: transparent;
    border: 0;
  }

  .zone-register {
    margin-top: 20px;

    button {
      width: 100%;
      background-color: #3fb0ac;
      border: none;

      &:focus {
        background-color: #f98404;
      }

      &:hover {
        opacity: 0.8;
      }
    }
  }
  .inputForm {
    background-color: transparent;
    &:focus {
      box-shadow: none;
    }
  }
  .zone-forgot-password {
    margin-top: 20px;

    button {
      width: 100%;
      background-color: #173e43;
      border: none;

      &:focus {
        background-color: #f98404;
      }

      &:hover {
        opacity: 0.8;
      }
    }
  }
  .label-name {
    font-size: 21px;
  }
}

</style>
