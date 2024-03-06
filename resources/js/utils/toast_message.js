import Vue from 'vue';

export const MakeToast = ({ variant = null, title, content }) => {
  const vm = new Vue();
  vm.$bvToast.toast(content, {
    title: title,
    variant: variant,
    toaster: 'b-toaster-top-center',
    solid: true,
    autoHideDelay: 2500,
    appendToast: true,
    toastClass: 'custom-toast',
  });
};
