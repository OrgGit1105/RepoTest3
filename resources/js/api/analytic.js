import request from '../utils/request.js';
import { template } from './template.js';

const urlAPI = {
  urlData: template`/analytic`,
  urlAnalyticDetail: template`/analytic/emotions`,
};

export function getAllAnalytic(data) {
  return request.getRequest(urlAPI.urlData(), data);
}

export function getEmotions(data) {
  return request.getRequest(urlAPI.urlAnalyticDetail(), data);
}