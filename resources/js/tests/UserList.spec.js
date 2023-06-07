/* eslint-disable no-undef */
import { shallowMount, createLocalVue } from '@vue/test-utils';
import store from '@/store';
import UserList from '@/views/User/index.vue';
import * as UserApi from '@/api/user';
import { postLogin } from '@/api/login';
import router from '@/router';
describe('Test Component UserList', () => {
  // const onSubmit = jest.fn();
  const localVue = createLocalVue();
  const wrapper = shallowMount(UserList, {
    localVue,
    store,
    router,
    globals: {
      UserApi: true,
    },
  });
  const toCreatePage = jest.fn();
  const goToEditScreen = jest.fn();
  wrapper.setMethods({ toCreatePage, goToEditScreen });
  const data = {
    userList: [],
    pagination: {
      current_page: 1,
      per_page: 20,
      total_records: 0,
    },
  };
  const params = {
    url: '/user',
    page: 1,
    per_page: 20,
  };
  let TOKEN = '';
  let PROFILE = {};
  let USER = {};
  test('Case 1: Check have button Sign Up', async() => {
    await wrapper.find('.btn-sign').trigger('click');
    expect(toCreatePage).toHaveBeenCalled();
  });
  // test('Case 2: Check have button Edit', async() => {
  //   const spy = jest.spyOn(wrapper.vm, 'goToEditScreen');
  //   const btnEdit = wrapper.findAll('.btn-edit');
  //   btnEdit.trigger('click');
  //   expect(spy).toHaveBeenCalled();
  // });
  test('Case 2: Check Login', async() => {
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
  });
  test('Case 3: Check get All user', async() => {
    // Step 2: Call api List user

    await UserApi.getAllUserTest(params)
      .then(res => {
        data.userList = res.data.result;
      })
      // eslint-disable-next-line handle-callback-err
      .catch(err => {
        // console.log(err);
      });
    await store.dispatch('app/saveListUSer', data.userList);
    expect(store.getters.listUser).toBe(data.userList);
  });
  test('Case 4: Test BTN Create Exits', () => {
    const CREATE = wrapper.find('.btn-sign');

    expect(CREATE.exists()).toBeTruthy();
  });
});
