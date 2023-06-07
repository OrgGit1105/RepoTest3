/* eslint-disable no-undef */
/* eslint-disable no-unused-vars */
import { shallowMount, createLocalVue } from '@vue/test-utils';
import Retirement from '@/views/Retirement/index.vue';
import { getAllRetirement } from '@/api/retirement';
import { postLogin } from '@/api/login';
import store from '@/store';
import router from '@/router';

describe('Test Component Retirement', () => {
  const localVue = createLocalVue();
  const wrapper = shallowMount(Retirement, {
    localVue,
    store,
    router,
  });
  wrapper.vm.closeLoading = jest.fn();
  const data = {
    listRetirement: [],
  };

  const TOKEN = '';
  const PROFILE = {};
  const USER = {};

  it('Case 1: Check component retirement', () => {
    expect(typeof Retirement.data).toBe('function');
  });

  it('Case 2: Test Button "Previous month" exits', () => {
    const timeButton = wrapper.find('.btn-select');
    expect(timeButton.exists()).toBeTruthy();
  });

  it('Case 3: Test Button "Next month" exits', () => {
    const timeButton = wrapper.find('.btn-select');
    expect(timeButton.exists()).toBeTruthy();
  });

  it('Case 4: Test click button "Previous month"', () => {
    const BTN_BACK = wrapper.find('.btn-prev');

    const spy = jest.spyOn(wrapper.vm, 'backTime');

    BTN_BACK.trigger('click');

    expect(spy).toHaveBeenCalled();
  });

  it('Case 5: Test click button "Next month"', () => {
    const BTN_NEXT = wrapper.find('.btn-next');

    const spys = jest.spyOn(wrapper.vm, 'nextTime');

    BTN_NEXT.trigger('click');

    expect(spys).toHaveBeenCalled();
  });

  it('Case 6: Test api get data retirement ', async() => {
    // Step 1: Login user
    const account = {
      url: '/auth/login',
      user_name: 'test@gmail.com',
      password: '12345678',
    };
    await postLogin(account)
      .then(res => {
        // eslint-disable-next-line no-const-assign
        TOKEN = res.data.access_token;
        // eslint-disable-next-line no-const-assign
        PROFILE = res.data.profile;

        // eslint-disable-next-line no-const-assign
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

      .catch(_err => {
      });
    expect(TOKEN).not.toBeNull();
    await store.dispatch('user/saveLogin', { USER, TOKEN });

    const params = {
      url: '/retirement',
    };

    await getAllRetirement(params)
      .then(res => {
        data.listRetirement = res.data;
      })
      // eslint-disable-next-line handle-callback-err
      .catch(_err => {
      });
    await store.dispatch('app/saveListRetirement', data.listRetirement);
    expect(store.getters.listRetirement).toBe(data.listRetirement);
  });

  test('Case 7: Test method openLoading is run when call method getAllRetirement()', async() => {
    wrapper.vm.openLoading = jest.fn();
    wrapper.vm.getListAllRetirement();
    expect(wrapper.vm.openLoading).toHaveBeenCalled();
  });

  test('Case 8: Test method closeLoading will run when the getAllRetirement() method has finished running', async() => {
    wrapper.vm.getListAllRetirement();
    const getAllRetirement = jest
      .fn()
      .mockResolvedValue();
    await getAllRetirement();
    expect(wrapper.vm.closeLoading).toHaveBeenCalled();
  });
});
