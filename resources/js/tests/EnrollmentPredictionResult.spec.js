/* eslint-disable no-undef */
/* eslint-disable no-unused-vars */
import { shallowMount, createLocalVue } from '@vue/test-utils';
import store from '@/store';
import router from '@/router';
import EnrollmentPredictionResult from '@/views/Enrollment/candidateResult.vue';
import * as EnrollmentApi from '@/api/enrollment';
import { postLogin } from '@/api/login';
describe('Test Component EnrollmentPredictionResult', () => {
  const localVue = createLocalVue();
  const wrapper = shallowMount(EnrollmentPredictionResult, {
    localVue,
    store,
    router,
    globals: {
      EnrollmentApi: true,
    },
  });

  const data = {
    listInfo: {},
    listEvaluation: {},
    listEmployee: {},
  };
  let TOKEN = '';
  let PROFILE = {};
  let USER = {};

  test('Case 1 Check function call Api getCandidateResult', async() => {
    const account = {
      url: '/auth/login',
      user_name: 'test@gmail.com',
      password: '12345678',
    };
    await postLogin(account)
      .then(res => {
        // console.log('Data return', res);
        TOKEN = res.data.access_token;
        PROFILE = res.data.profile;

        USER = {
          address: PROFILE.address || '',
          avatar: PROFILE.avatar || '',
          email: PROFILE.email || '',
          fax: PROFILE.fax || '',
          gender: PROFILE.gender || '',
          id: PROFILE.id || '',
          name: PROFILE.name || '',
          phone: PROFILE.phone || '',
          status: PROFILE.status || '',
        };
      })
      // eslint-disable-next-line handle-callback-err
      .catch(err => {
        // console.log(err);
      });
    expect(TOKEN).not.toBeNull();
    await store.dispatch('user/saveLogin', { USER, TOKEN });
    // Case 2: Call api get Result
    HTMLCanvasElement.prototype.getContext = jest.fn();
    const params = {
      url: '/enrollment',
      id: 1,
    };
    await EnrollmentApi.getResult(params)
      .then(res => {
        data.listInfo = res.data.enrollment;
        data.listEvaluation = res.data.config;
        data.listEmployee = res.data.users;
        // console.log('res.data', res);
      })
      // eslint-disable-next-line handle-callback-err
      .catch(err => {
        // console.log('err ==>', err);
      });
    // Case3: Check Data Store ListInFo
    await store.dispatch('app/saveListInfo', data.listInfo);
    expect(store.getters.listInfo).toBe(data.listInfo);
    // Case4: Check Data Store ListEvaluation
    await store.dispatch('app/saveListEvaluation', data.listEvaluation);
    expect(store.getters.listEvaluation).toBe(data.listEvaluation);
    // Case5: Check Data Store listEmployee
    await store.dispatch('app/saveListEmployee', data.listEmployee);
    expect(store.getters.listEmployee).toBe(data.listEmployee);
  });
  test('Case 6: Test Result is EXIT -> Show Result', async() => {
    await wrapper.setData({
      isExit: null,
    });

    const FillterList = wrapper.findComponent({ name: 'FillterList' });
    const Result = wrapper.findComponent({ name: 'Result' });

    expect(FillterList.exists()).toBe(true);
    expect(Result.exists()).toBe(true);
  });
  test('Case 7: Test BUTTON Return Exits', () => {
    const BTN_RETURN = wrapper.find('.btn-return');

    expect(BTN_RETURN.exists()).toBeTruthy();
  });
  // test('Case 8: Test function when click Button Back in Map Data', async() => {
  //   await wrapper.vm.returnToList();
  //   await wrapper.vm.$nextTick(() => {
  //     const url = window.location.href;
  //     console.log('Manh dang tim', url);
  //     expect(url.includes(`/enrollment/index`));
  //   });
  // });
});
