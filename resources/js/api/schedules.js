import request from '../utils/request.js';
import { template } from './template.js';

export const urlAPI = {
  urlData: template`/schedule`,
  exportDAta: template`/schedule/export`,
};

export function getAllSchedules(data) {
  return request.getRequest(urlAPI.urlData(), data);
}

export function exportSchedules(data) {
  return request.getRequest(urlAPI.exportDAta(), data);
}
