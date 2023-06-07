/* eslint-disable indent */
/* eslint-disable handle-callback-err */
/* eslint-disable no-undef */
import { shallowMount, createLocalVue } from '@vue/test-utils';
import EmployeeDetail from '@/views/Employee/chart.vue';
import { getEmployeeId } from '@/api/employee';
import { postLogin } from '@/api/login';
import store from '@/store';
import router from '@/router';

describe('Test Component Employee Detail', () => {
  const localVue = createLocalVue();
  const wrapper = shallowMount(EmployeeDetail, {
    localVue,
    store,
    router,
  });

  const data = {
    employeeDetailInfo: {},
    employeeDetailList: [],
  };
  wrapper.vm.closeLoading = jest.fn();

  let TOKEN = '';
  let PROFILE = {};
  let USER = {};

  test('Case 1: Check component name Employee Detail', async() => {
    expect(wrapper.vm.$options.name).toMatch('EmployeeDetail');
  });

  test('Case 2: Test Button Return Exits', () => {
    const returnButton = wrapper.find('.btn-back');
    expect(returnButton.exists()).toBeTruthy();
  });

  test('Case 3: Test Button Time Exits', () => {
    const timeButton = wrapper.find('.btn-time');
    expect(timeButton.exists()).toBeTruthy();
  });

  test('Case 4: Test Get Info Employee Detail', async() => {
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

    const params = {
      url: '/employeedetail',
      employeeId: '1111-2222-0002',
      timeYear: '2021',
    };
    await getEmployeeId(params)
      .then(res => {
        data.employeeDetailList = res.data[0].risk_score;
        data.dataReturn = res.data[0];
        data.employeeInfo = { employeeCode: dataReturn.employee_code, employeeName: dataReturn.employee_name, companyName: dataReturn.company.name };
      })
      .catch(err => {
      });
    await store.dispatch('app/saveEmployeeDetailInfo', data.employeeInfo);
    await store.dispatch('app/saveEmployeeDetailList', data.employeeDetailList);
    expect(store.getters.employeeInfo).toBe(data.employeeInfo);
  });

  test('Case 5: Test method openLoading is run when call method getEmployeeInfo()', async() => {
    wrapper.vm.openLoading = jest.fn();
    wrapper.vm.getEmployeeInfo();
    expect(wrapper.vm.openLoading).toHaveBeenCalled();
  });

  test('Case 6: Test method closeLoading will run when the getEmployeeInfo() method has finished running', async() => {
  wrapper.vm.getEmployeeInfo();
    const getEmployeeById = jest
    .fn()
    .mockResolvedValue();
    await getEmployeeById();
    expect(wrapper.vm.closeLoading).toHaveBeenCalled();
  });
});
