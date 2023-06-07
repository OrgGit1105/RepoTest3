/* eslint-disable no-undef */
/* eslint-disable no-unused-vars */
import { shallowMount, createLocalVue } from '@vue/test-utils';
import DataDigitacoDetail from '@/views/DataDigitaco/detail.vue';
import { getOneDataDigitaco } from '@/api/data_digitaco';
import { postLogin } from '@/api/login';
import store from '@/store';
import router from '@/router';

describe('Test Component DataDigitacoDetail', () => {
  const localVue = createLocalVue();
  const wrapper = shallowMount(DataDigitacoDetail, {
    localVue,
    store,
    router,
  });

  const data = {
    detailDataDigitaco: [],
    queryData: {
      current_page: 1,
      per_page: 100,
      total_records: 0,
    },
  };
  let TOKEN = '';
  let PROFILE = {};
  let USER = {};

  wrapper.vm.closeLoading = jest.fn();
  it('Case 1: Check component Data Digitaco list Detail has data', () => {
    expect(typeof DataDigitacoDetail.data).toBe('function');
  });

  it('Case 2: Test when click button "Back" ', async() => {
    await wrapper.find('.btn-back').trigger('click');
    await wrapper.vm.$nextTick(() => {
      expect(wrapper.vm.$route.path).toBe(`/datadigitaco/index`);
    });
  });

  it('Case 3: Test funtion call API show Data Digitaco Detail', async() => {
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
      .catch(_err => {
        // console.log(err);
      });
    expect(TOKEN).not.toBeNull();
    await store.dispatch('user/saveLogin', { USER, TOKEN });

    const params = {
      url: '/digitacoDetail',
      id: '1',
      type: 'point',
    };
    await getOneDataDigitaco(params)
      .then(res => {
        data.detailDataDigitaco = res.data.data;
      })
      // eslint-disable-next-line handle-callback-err
      .catch(err => {
      });
    await store.dispatch('app/saveDetailDataDigitaco', data.detailDataDigitaco);
    expect(store.getters.detailDataDigitaco).toBe(data.detailDataDigitaco);
  });

  it('Case 4: Should data digitaco with pagination', async() => {
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
      .catch(_err => {
      });

    expect(TOKEN).not.toBeNull();

    await store.dispatch('user/saveLogin', { USER, TOKEN });
    const pagination = {
      url: '/digitacoDetail',
      page: data.queryData.current_page,
      per_page: data.queryData.per_page,
      id: '1',
      type: 'point',
    };
    await getOneDataDigitaco(pagination)
      .then(res => {
        data.detailDataDigitaco = res.data.data;
      })
      // eslint-disable-next-line handle-callback-err
      .catch((error) => {
      });
    await store.dispatch('app/saveDetailDataDigitaco', data.detailDataDigitaco);
    expect(store.getters.detailDataDigitaco).toBe(data.detailDataDigitaco);
  });
  test('Case 5: Test method openLoading is run when call method getDataDetail()', async() => {
    wrapper.vm.openLoading = jest.fn();
    wrapper.vm.getDataDetail();
    expect(wrapper.vm.openLoading).toHaveBeenCalled();
  });

  test('Case 6: Test method closeLoading will run when the getDataDetail() method has finished running', async() => {
    wrapper.vm.getDataDetail();
    const getOneDataDigitaco = jest
      .fn()
      .mockResolvedValue();
    await getOneDataDigitaco();
    expect(wrapper.vm.closeLoading).toHaveBeenCalled();
  });
});
