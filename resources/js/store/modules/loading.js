// import Cookies from 'js-cookie';

// function getStateLoading() {
//   const stateLoading = Cookies.get('stateLoading');
//   console.log('state Loading', stateLoading);
//   if (stateLoading) {
//     return stateLoading;
//   }
//   return false;
// }

const state = {
  stateLoading: false,
};

const mutations = {
  SET_LOADING: (state, stateLoading) => {
    state.stateLoading = stateLoading;
    // console.log('da chay vao day', state.stateLoading);
    // Cookies.set('stateLoading', stateLoading);
  },
};
const actions = {
  setLoading({ commit }, stateLoading) {
    commit('SET_LOADING', stateLoading);
  },
};
// const getters = {
//   loading(state) {
//     return state.loading;
//   },
// };
export default {
  namespaced: true,
  state,
  mutations,
  actions,
  // getters,
};
