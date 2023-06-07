/* eslint-disable semi */
/* eslint-disable no-const-assign */
/* eslint-disable no-import-assign */
/* eslint-disable indent */
/* eslint-disable handle-callback-err */
/* eslint-disable no-undef */
/* eslint-disable no-unused-vars */
import { shallowMount, createLocalVue } from '@vue/test-utils';
import EmployeeList from '@/views/Employee/index.vue';
import store from '@/store';
import router from '@/router';
import { UserRoleId } from '@/configs';
import Vuex from 'vuex';

describe('Test Component EmployeeList', () => {
  const localVue = createLocalVue();
  const wrapper = shallowMount(EmployeeList, {
    localVue,
    store,
    router,
  });
  const data = {
    listEmployee: [],
    listBranch: [],
    branchUser: [],
    queryData: {
      page: 1,
      per_page: 20,
      total_records: 0,
      company_branch: '',
      employee_code: '',
      employee_name: '',
    },
  };
  test('Case 1: Check component name EmployeeList', async() => {
    expect(wrapper.vm.$options.name).toMatch('EmployeeList');
  });

  test('Case 2: Test Button Checkbox Departure Exits', () => {
    const Checkbox = wrapper.find('#cbDeparture');

    expect(Checkbox.exists()).toBeTruthy();
  });

  test('Case 3: Test Button Checkbox Employee Number Exits', () => {
    const Checkbox = wrapper.find('#cbEmployeeNumber');
    expect(Checkbox.exists()).toBeTruthy();
  });

  test('Case 4: Test Button Checkbox Employee Name Exits', () => {
    const Checkbox = wrapper.find('#cbEmployeeName');
    expect(Checkbox.exists()).toBeTruthy();
  });

  test('Case 5: Test Button Apply Exits', () => {
    const btnApply = wrapper.find('.btn-apply');
    expect(btnApply.exists()).toBeTruthy();
  });

  test('Case 6: Test handle submit button "Search" ', () => {
    const btnSubmit = wrapper.find('.btn-apply');

    const spys = jest.spyOn(wrapper.vm, 'onSubmit');

    btnSubmit.trigger('submit');

    expect(spys).toHaveBeenCalled();
  });

  test('Case 7: check show form with role', async() => {
    // If Headquater
    await wrapper.setData({
      roleId: UserRoleId.HEAD_QUARTER,
    });
    expect(wrapper.find('.show-by-role').isVisible()).toBe(true);
    // If Department
    await wrapper.setData({
      roleId: UserRoleId.DEPARTMENT,
    });
    expect(wrapper.find('.show-by-role').isVisible()).toBe(false);
  });

  test('Case 8: check show form select when click checkbox in departure', async() => {
    await wrapper.setData({
      roleId: UserRoleId.HEAD_QUARTER,
      statusDepature: 'accepted',
    });
    expect(wrapper.find('.depature-status').isVisible()).toBe(true);
    await wrapper.setData({
      statusDepature: 'not_accepted',
    });
    expect(wrapper.find('.depature-status').isVisible()).toBe(false);
  });

  test('Case 9: check show form select when click checkbox in employee code', async() => {
    await wrapper.setData({
      statusEmployeeNumber: 'accepted',
    });
    expect(wrapper.find('.employee-status').isVisible()).toBe(true);
    await wrapper.setData({
      statusEmployeeNumber: 'not_accepted',
    });
    expect(wrapper.find('.employee-status').isVisible()).toBe(false);
  });

  test('Case 10: check show form select when click checkbox in employee name', async() => {
    await wrapper.setData({
      statusEmployeeName: 'accepted',
    });
    expect(wrapper.find('.employee-name-status').isVisible()).toBe(true);
    await wrapper.setData({
      statusEmployeeName: 'not_accepted',
    });
    expect(wrapper.find('.employee-name-status').isVisible()).toBe(false);
  });

  // test('Case 11: Check get All Employee List', async() => {
  //   wrapper.vm.getAllEmployee();
  //   const getAllEmployee = jest
  //     .fn()
  //     .mockResolvedValue();
  //   await getAllEmployee()
  //     .then(res => {
  //       data.listEmployee = res.data.data.result;
  //     })
  //     .catch(err => {
  //       // console.log(err);
  //     });
  //   // console.log('listEmployee ==>', data.listEmployee);
  //   await store.dispatch('app/saveListEmployee', data.listEmployee);
  //   expect(store.getters.listEmployee).toBe(data.listEmployee);
  // });

  // test('Case 12: Check get All Data company branch', async() => {
  //   wrapper.vm.getAllCompanyBranch();
  //   const getAllCompanyBranch = jest
  //     .fn()
  //     .mockResolvedValue();
  //   await getAllCompanyBranch()
  //   .then(res => {
  //     data.listBranch = res.data.result;
  //   })
  //   .catch(err => {
  //     // console.log(err);
  //   });
  //   await store.dispatch('app/saveListBranch', data.listBranch);
  //   expect(store.getters.listBranch).toBe(data.listBranch);
  // });

  // test('Case 13: Check get All Data company branch by user', async() => {
  //   wrapper.vm.getCompanyBranchByUser();
  //   const getCompanyBranchByUser = jest
  //   .fn()
  //   .mockResolvedValue();
  //   await getCompanyBranchByUser()
  //   .then(res => {
  //     data.branchUser = res.data;
  //   })
  //   .catch(err => {
  //     // console.log(err);
  //   });
  //   await store.dispatch('app/saveBranchUser', data.branchUser);
  //   expect(store.getters.branchUser).toBe(data.branchUser);
  // });
  // test('Case 14: Test call api getAllCompanyBranch with role_id equal 1', async() => {
  //   const getAllCompanyBranch = jest.fn();
  //   await getAllCompanyBranch();
  //   expect(getAllCompanyBranch).toHaveBeenCalled();
  // });
  // test('Case 15: Test call api getCompanyBranchByUser with role_id equal 2', async() => {
  //   const getCompanyBranchByUser = jest.fn();
  //   await getCompanyBranchByUser();
  //   expect(getCompanyBranchByUser).toHaveBeenCalled();
  // });
});
