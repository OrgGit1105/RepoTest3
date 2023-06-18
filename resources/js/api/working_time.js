import request from '../utils/request.js';
import { template } from './template.js';

const urlAPI = {
  urlData: template`/arriving_report`,
};

export function getArrving(data) {
  return request.getRequest(urlAPI.urlData(), data);
}

export function createNewWorkingTime(data) {
  return request.postRequest(urlAPI.urlData(), data);
}

// export function getAllEmployee(data) {
//   return request.getRequest(urlAPI.urlEmployee(), data);
// }

// export function getEmployeeById(data) {
//   return request.getRequest(urlAPI.urlGetById(), data);
// }
