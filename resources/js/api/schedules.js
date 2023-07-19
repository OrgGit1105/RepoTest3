import request from '../utils/request.js';
import { template } from './template.js';

const urlAPI = {
    urlData: template`/schedule`,
};

export function getAllSchedules(data) {
    return request.getRequest(urlAPI.urlData(), data);
}