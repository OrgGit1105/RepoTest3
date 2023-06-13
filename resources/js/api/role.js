import request from '../utils/request.js';
import { template } from './template.js';
const urlAPI = {
  urlGETRole: template`/api/role`,
};

export function getAllRole() {
  return request.getRequest(urlAPI.urlGETRole());
}

