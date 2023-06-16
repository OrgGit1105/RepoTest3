// import { defaultsDeep } from 'lodash';
import request from '../utils/request.js';
// import * as RequestApi from './request';
import { template } from './template.js';

const urlAPI = {
  urlData: template`/arriving_report`,
};

export function getArrving(data) {

  return request.getRequest(urlAPI.urlData(), data);
}

// export function getAllData(data) {
//   return request.getRequest(urlAPI.urlData(), data);
// }
// export function getAllEmployee(data) {
//   return request.getRequest(urlAPI.urlEmployee(), data);
// }

// export function getEmployeeById(data) {
//   return request.getRequest(urlAPI.urlGetById(), data);
// }
