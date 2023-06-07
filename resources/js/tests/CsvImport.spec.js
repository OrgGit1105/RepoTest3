/* eslint-disable quotes */
/* eslint-disable no-undef */
import { shallowMount, createLocalVue } from "@vue/test-utils";
import store from '@/store';
import CsvImport from '@/views/CSV_Import/index.vue';
describe('Test Component CSV Import', () => {
  const event = {
    target: {
      files: [
        {
          name: 'data.csv',
          size: 50000,
          type: 'text/csv',
        },
      ],
    },
  };

  const selectFile = jest.fn();
  const onSubmit = jest.fn();
  const localVue = createLocalVue();
  const wrapper = shallowMount(CsvImport, {
    localVue,
    store,
  });

  wrapper.setMethods({ selectFile }, { onSubmit });

  test('Case 1: Check component name CSV', async() => {
    expect(wrapper.vm.$options.name).toMatch('CSV');
  });

  test('Case 2: Initializes with the two buttons', () => {
    // check that 2 buttons are created and are disabled
    expect(wrapper.findAll('button').length).toEqual(2);
    expect(wrapper.findAll('button').at(0).text()).toMatch('LANGUAGES.TEXT_SELECT_FILE');
    expect(wrapper.findAll('button').at(1).text()).toMatch('LANGUAGES.TEXT_PERFORM_AN_IMPORT');
  });

  test('Case 3: Check handle selectFile', async() => {
    wrapper.find('.btn-select').trigger('click');
    expect(selectFile).toHaveBeenCalled();
  });

  test('Case 4: Check upload csv file', async() => {
    const fileReaderSpy = jest.spyOn(FileReader.prototype, 'readAsBinaryString').mockImplementation(() => null);
    wrapper.vm.uploadCSV(event);
    expect(fileReaderSpy).toHaveBeenCalledWith(event.target.files[0]);
  });
  test('Case 5: Check button select file is exit', async() => {
    const BTN_SELECT = wrapper.find('.btn-select');
    expect(BTN_SELECT.exists()).toBeTruthy();
  });
  test('Case 6: Check button import is exit', async() => {
    const BTN_IMPORT = wrapper.find('.btn-import');
    expect(BTN_IMPORT.exists()).toBeTruthy();
  });

  // test('Case 7: Test click BUTTON PERFORM AN IMPORT', () => {
  //   wrapper.find('.btn-import').trigger('click');
  //   expect(onSubmit).toHaveBeenCalled();
  // });

  // test('Case 2: Check handle input change', async() => {
  //   // const file_input = wrapper.find("input[type=file]");
  //   // file_input.element.files[0] = { type: "text/html" };
  //   // file_input.trigger("change");

  //   // expect(wrapper.emitted()["invalid-file"]).toBeDefined();

  //   const input = wrapper.find('input[type="file"]');
  //   const dT = new ClipboardEvent('').clipboardData || new DataTransfer();
  //   dT.items.add(new File(['foo'], 'programmatically_created.txt'));
  //   input.element.files = dT.files;
  //   input.trigger('change');
  // });
});
