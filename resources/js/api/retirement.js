import request from '../utils/request.js';
import { template } from './template.js';

const urlAPI = {
  urlDate: template`/retirement_prediction_chart/show`,
};

export function getAllRetirement(data) {
  return request.getRequest(data.url, data.query);
}

export function getMonthYear() {
  return request.getRequest(urlAPI.urlDate());
}
