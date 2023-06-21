import request from '../utils/request.js';
import { template } from './template.js';

const urlAPI = {
  urlData: template`/arriving_report`,
  urlGetById: template`/arriving_report/${'id'}`,
};

export function getArrving(data) {
  return request.getRequest(urlAPI.urlData(), data);
}

export function createNewWorkingTime(data) {
  return request.postRequest(urlAPI.urlData(), data);
}

export function getWokingTimeDetailById(id, data) {
  return request.getRequest(urlAPI.urlGetById(id), data);
}

export function editWorkingTimeById(id, data) {
  return request.putRequest(urlAPI.urlGetById(id), data);
}

export function deleteWorkingTimeById(id, data) {
  return request.deleteRequest(urlAPI.urlGetById(id), data);
}
