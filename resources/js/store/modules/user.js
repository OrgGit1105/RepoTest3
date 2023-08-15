import Cookies from 'js-cookie';
import { getToken } from '../../utils/getToken';
const _ = require('lodash');

function getUserInfo() {
  const USER_INFO = Cookies.get('userInfo');

  if (USER_INFO) {
    return JSON.parse(USER_INFO);
  }

  const USER = {
    address: '',
    avatar: '',
    email: '',
    fax: '',
    gender: '',
    id: '',
    name: '',
    phone: '',
    status: '',
    role_id: '',
    username: '',
  };

  return USER;
}

const state = {
  userInfo: getUserInfo(),
  token: getToken(),
};

const mutations = {
  SET_ADDRESS: (state, address) => {
    state.userInfo.address = address;
  },
  SET_AVATAR: (state, avatar) => {
    state.userInfo.avatar = avatar;
  },
  SET_EMAIL: (state, email) => {
    state.userInfo.email = email;
  },
  SET_FAX: (state, fax) => {
    state.userInfo.fax = fax;
  },
  SET_GENDER: (state, gender) => {
    state.userInfo.gender = gender;
  },
  SET_ID: (state, id) => {
    state.userInfo.id = id;
  },
  SET_NAME: (state, name) => {
    state.userInfo.name = name;
  },
  SET_PHONE: (state, phone) => {
    state.userInfo.phone = phone;
  },
  SET_STATUS: (state, status) => {
    state.userInfo.status = status;
  },
  SET_TOKEN: (state, token) => {
    state.token = token;
  },
  SET_ROLEID: (state, role_id) => {
    state.userInfo.role_id = role_id;
  },
  SET_USERNAME: (state, username) => {
    state.userInfo.username = username;
  },
};

const actions = {
  saveLogin({ commit }, userInfo) {
    commit('SET_ADDRESS', _.get(userInfo.USER, 'address', ''));
    commit('SET_AVATAR', _.get(userInfo.USER, 'avatar', ''));
    commit('SET_EMAIL', _.get(userInfo.USER, 'email', ''));
    commit('SET_FAX', _.get(userInfo.USER, 'fax', ''));
    commit('SET_GENDER', _.get(userInfo.USER, 'gender', ''));
    commit('SET_ID', _.get(userInfo.USER, 'id', ''));
    commit('SET_NAME', _.get(userInfo.USER, 'name', ''));
    commit('SET_PHONE', _.get(userInfo.USER, 'phone', ''));
    commit('SET_STATUS', _.get(userInfo.USER, 'status', ''));
    commit('SET_ROLEID', _.get(userInfo.USER, 'role_id', ''));
    commit('SET_USERNAME', _.get(userInfo.USER, 'username', ''));
    commit('SET_TOKEN', userInfo.TOKEN);
    Cookies.set('token', userInfo.TOKEN);
    Cookies.set('userInfo', userInfo.USER);
  },

  logout({ commit }) {
    commit('SET_ADDRESS', '');
    commit('SET_AVATAR', '');
    commit('SET_EMAIL', '');
    commit('SET_FAX', '');
    commit('SET_GENDER', '');
    commit('SET_ID', '');
    commit('SET_NAME', '');
    commit('SET_PHONE', '');
    commit('SET_STATUS', '');
    commit('SET_ROLEID', '');
    commit('SET_USERNAME', '');
    commit('SET_TOKEN', '');

    Cookies.set('token', '');
    Cookies.set('userInfo', '');
    Cookies.set('startDate', '');
    Cookies.set('endDate', '');
  },
};

// const getters = {
//   userInfo(state) {
//     return state.userInfo;
//   },
//   address(state) {
//     return state.userInfo.address;
//   },
//   avatar(state) {
//     return state.userInfo.avatar;
//   },
//   email(state) {
//     return state.userInfo.email;
//   },
//   fax(state) {
//     return state.userInfo.fax;
//   },
//   gender(state) {
//     return state.userInfo.gender;
//   },
//   id(state) {
//     return state.userInfo.id;
//   },
//   name(state) {
//     return state.userInfo.name;
//   },
//   phone(state) {
//     return state.userInfo.phone;
//   },
//   status(state) {
//     return state.userInfo.status;
//   },
//   token(state) {
//     return state.token;
//   },
// };

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  // getters,
};
