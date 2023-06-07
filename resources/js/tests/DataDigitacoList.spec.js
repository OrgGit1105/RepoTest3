/* eslint-disable no-undef */
/* eslint-disable no-unused-vars */
import { shallowMount, createLocalVue } from '@vue/test-utils';
import DataDigitacoList from '@/views/DataDigitaco/index.vue';
import { getAllDataDigitaco } from '@/api/data_digitaco';
import { postLogin } from '@/api/login';
import store from '@/store';
import router from '@/router';

describe('Test Component EmployeeList', () => {
  const localVue = createLocalVue();
  const wrapper = shallowMount(DataDigitacoList, {
    localVue,
    store,
    router,

  });
  wrapper.vm.closeLoading = jest.fn();
  const data = {
    listDataDigitaco: [],
    queryData: {
      current_page: 1,
      per_page: 20,
      total_records: 0,
    },
  };
  let TOKEN = '';
  let PROFILE = {};
  let USER = {};

  it('Case 1: Check component Data Digitaco list has data', () => {
    expect(typeof DataDigitacoList.data).toBe('function');
  });

  it('Case 2: Test function when click button Detail (Point list)', () => {
    const $route = {
      path: '/datadigitaco/detail/${id}/${type}',
    };
    wrapper.vm.nextToResult($route.path);
  });

  it('Case 3: Test function when click button Detail (Personal safe driving list)', () => {
    const $route = {
      path: '/datadigitaco/detail/${id}/${type}',
    };
    wrapper.vm.nextToResult($route.path);
  });

  it('Case 4: Check get All Data Digitaco List', async() => {
    // Step 1: Login user
    const account = {
      url: '/auth/login',
      user_name: 'test@gmail.com',
      password: '12345678',
    };
    await postLogin(account)
      .then(res => {
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
    // console.log("USER ==>", USER);
    await store.dispatch('user/saveLogin', { USER, TOKEN });

    // Step 2: Call api List Digitaco
    const pagination = {
      url: '/digitacoList',
      page: data.queryData.current_page,
      per_page: data.queryData.per_page,
    };

    await getAllDataDigitaco(pagination)
      .then(res => {
        data.listDataDigitaco = res.data.result;
      })
      // eslint-disable-next-line handle-callback-err
      .catch(err => {
        // console.log(err);
      });
    await store.dispatch('app/saveDataDigitaco', data.listDataDigitaco);
    expect(store.getters.listDataDigitaco).toBe(data.listDataDigitaco);
  });

  test('Case 5: Test method openLoading is run when call method getListAllData()', async() => {
    wrapper.vm.openLoading = jest.fn();
    wrapper.vm.getListAllData();
    expect(wrapper.vm.openLoading).toHaveBeenCalled();
  });

  test('Case 6: Test method closeLoading will run when the getListAllData() method has finished running', async() => {
    wrapper.vm.getListAllData();
    const getAllDataDigitaco = jest
      .fn()
      .mockResolvedValue();
    await getAllDataDigitaco();
    expect(wrapper.vm.closeLoading).toHaveBeenCalled();
  });
});
