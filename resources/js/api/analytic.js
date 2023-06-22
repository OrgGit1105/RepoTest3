import request from '../utils/request.js';
import { template } from './template.js';

const urlAPI = {
  urlData: template`/analytic`,
};

export function getAllAnalytic(data) {
  return request.getRequest(urlAPI.urlData(), data);
}
